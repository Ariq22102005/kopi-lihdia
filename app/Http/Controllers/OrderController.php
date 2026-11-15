<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Riwayat pesanan milik user yang sedang login
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        $order->load('items');

        return view('orders.show', compact('order'));
    }

    public function invoice(Request $request, Order $order)
    {
        $this->authorizeOwner($request, $order);

        $order->load('items', 'user');

        return view('orders.invoice', compact('order'));
    }

    // Customer hanya boleh melihat pesanan miliknya sendiri
    private function authorizeOwner(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 403);
    }
}
