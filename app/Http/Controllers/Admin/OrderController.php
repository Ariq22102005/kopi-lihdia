<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    // Alur status pemrosesan: hanya boleh maju satu langkah
    private const NEXT_STATUS = [
        Order::STATUS_PENDING    => [Order::STATUS_PROCESSING],
        Order::STATUS_PROCESSING => [Order::STATUS_SHIPPED],
        Order::STATUS_SHIPPED    => [Order::STATUS_COMPLETED],
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $orders = Order::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('order_number', 'like', "%{$search}%")
                      ->orWhere('recipient_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('payment'), fn ($q) => $q->where('payment_status', $request->query('payment')))
            ->when($request->filled('status'), fn ($q) => $q->where('order_status', $request->query('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'search'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'user');

        $nextOptions = self::NEXT_STATUS[$order->order_status] ?? [];
        $canUpdate = $order->payment_status === Order::PAYMENT_PAID && ! empty($nextOptions);

        return view('admin.orders.show', compact('order', 'nextOptions', 'canUpdate'));
    }

    /**
     * Admin hanya mengubah order_status (pemrosesan/pengiriman).
     * payment_status TIDAK PERNAH diubah di sini: hanya Midtrans
     * (lewat PaymentStatusUpdater) yang boleh mengubahnya.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if ($order->payment_status !== Order::PAYMENT_PAID) {
            return back()->with('error', 'Pesanan belum dibayar, status pemrosesan belum dapat diubah.');
        }

        $allowed = self::NEXT_STATUS[$order->order_status] ?? [];

        if (empty($allowed)) {
            return back()->with('error', 'Status pesanan ini sudah final.');
        }

        $validated = $request->validate([
            'order_status' => ['required', Rule::in($allowed)],
        ]);

        $order->update(['order_status' => $validated['order_status']]);

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
