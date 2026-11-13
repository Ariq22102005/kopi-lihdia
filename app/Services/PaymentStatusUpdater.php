<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentStatusUpdater
{
    /**
     * Terapkan hasil dari Midtrans (notifikasi atau Status API) ke order.
     * Satu-satunya tempat payment_status diubah.
     */
    public function apply(Order $order, array $payload): void
    {
        $transaction = $payload['transaction_status'] ?? null;
        $fraud       = $payload['fraud_status'] ?? null;
        $type        = $payload['payment_type'] ?? null;

        $newStatus = match (true) {
            $transaction === 'capture' => match ($fraud) {
                'challenge' => Order::PAYMENT_PENDING,
                'deny'      => Order::PAYMENT_FAILED,
                default     => Order::PAYMENT_PAID,
            },
            $transaction === 'settlement' => Order::PAYMENT_PAID,
            $transaction === 'pending'    => Order::PAYMENT_PENDING,
            in_array($transaction, ['deny', 'cancel', 'failure'], true) => Order::PAYMENT_FAILED,
            $transaction === 'expire'     => Order::PAYMENT_EXPIRED,
            default                       => null,
        };

        if ($newStatus === null) {
            return;
        }

        DB::transaction(function () use ($order, $newStatus, $type) {
            // Kunci baris order supaya notifikasi ganda tidak diproses bersamaan
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            // Hanya order yang masih pending yang boleh berubah (idempotent,
            // dan notifikasi yang datang terlambat tidak menurunkan status "paid")
            if ($order->payment_status !== Order::PAYMENT_PENDING) {
                return;
            }

            if ($newStatus === Order::PAYMENT_PENDING) {
                if ($type && $order->payment_type !== $type) {
                    $order->update(['payment_type' => $type]);
                }
                return;
            }

            if ($newStatus === Order::PAYMENT_PAID) {
                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                    'payment_type'   => $type,
                    'paid_at'        => now(),
                ]);
                Log::info("Order {$order->order_number} dibayar ({$type}).");
                return;
            }

            // Gagal atau kedaluwarsa: batalkan pesanan dan kembalikan stok
            $order->load('items');
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'payment_status' => $newStatus,
                'payment_type'   => $type ?? $order->payment_type,
                'order_status'   => Order::STATUS_CANCELLED,
            ]);
            Log::info("Order {$order->order_number} {$newStatus}, stok dikembalikan.");
        });
    }
}
