<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MidtransService;
use App\Services\PaymentStatusUpdater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Membuat (atau memakai ulang) Snap token untuk order milik user.
     */
    public function token(Request $request, Order $order, MidtransService $midtrans)
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->payment_status !== Order::PAYMENT_PENDING) {
            return response()->json(['message' => 'Pesanan ini tidak dapat dibayar lagi.'], 422);
        }

        if (! config('midtrans.server_key')) {
            return response()->json(['message' => 'Kunci Midtrans belum dikonfigurasi.'], 500);
        }

        try {
            if (! $order->snap_token) {
                $order->update(['snap_token' => $midtrans->createSnapToken($order)]);
            }
        } catch (\Throwable $e) {
            Log::error('Gagal membuat Snap token: ' . $e->getMessage());

            return response()->json(['message' => 'Gagal menghubungi Midtrans. Coba lagi.'], 502);
        }

        return response()->json(['token' => $order->snap_token]);
    }

    /**
     * Cek status ke Midtrans (Status API) lalu perbarui order.
     * Dipakai setelah popup pembayaran ditutup, dan berguna saat
     * webhook belum bisa diterima (misalnya di localhost).
     */
    public function check(
        Request $request,
        Order $order,
        MidtransService $midtrans,
        PaymentStatusUpdater $updater
    ) {
        abort_unless($order->user_id === $request->user()->id, 403);

        try {
            $status = $midtrans->fetchStatus($order->order_number);
            $updater->apply($order, $status);
        } catch (\Throwable $e) {
            Log::warning("Cek status {$order->order_number}: " . $e->getMessage());

            $message = 'Belum ada transaksi pembayaran untuk pesanan ini.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 404)
                : back()->with('error', $message);
        }

        $order->refresh();

        if ($request->expectsJson()) {
            return response()->json(['payment_status' => $order->payment_status]);
        }

        return back()->with('success', 'Status pembayaran diperbarui.');
    }
}
