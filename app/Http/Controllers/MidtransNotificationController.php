<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentStatusUpdater;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    public function handle(Request $request, PaymentStatusUpdater $updater)
    {
        $serverKey = (string) config('midtrans.server_key');
        if ($serverKey === '') {
            return response()->json(['message' => 'Server key belum dikonfigurasi.'], 500);
        }

        $payload = $request->json()->all() ?: $request->all();

        $orderId     = (string) ($payload['order_id'] ?? '');
        $statusCode  = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? ''); // string persis dari Midtrans, mis. "36000.00"
        $signature   = (string) ($payload['signature_key'] ?? '');

        // 1. Verifikasi signature: SHA512(order_id + status_code + gross_amount + server_key)
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (! hash_equals($expected, $signature)) {
            Log::warning("Notifikasi Midtrans dengan signature tidak valid (order {$orderId}).");

            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        // 2. Cari order
        $order = Order::where('order_number', $orderId)->first();
        if (! $order) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        // 3. Nominal harus sama dengan total di database
        if ((int) round((float) $grossAmount) !== (int) $order->total) {
            Log::warning("Nominal notifikasi tidak cocok untuk order {$orderId}.");

            return response()->json(['message' => 'Nominal tidak cocok.'], 400);
        }

        // 4. Perbarui status (idempotent)
        $updater->apply($order, $payload);

        return response()->json(['message' => 'OK']);
    }
}
