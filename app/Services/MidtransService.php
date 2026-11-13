<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = (string) config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Minta Snap token ke Midtrans. Nominal diambil dari database (order),
     * bukan dari browser.
     */
    public function createSnapToken(Order $order): string
    {
        $order->loadMissing('items', 'user');

        $items = $order->items->map(fn ($item) => [
            'id'       => (string) ($item->product_id ?? $item->id),
            'price'    => (int) $item->price,
            'quantity' => (int) $item->quantity,
            'name'     => Str::limit($item->product_name, 50, ''),
        ])->values()->all();

        if ($order->shipping_cost > 0) {
            $items[] = [
                'id'       => 'ONGKIR',
                'price'    => (int) $order->shipping_cost,
                'quantity' => 1,
                'name'     => 'Ongkos kirim',
            ];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total, // harus sama dengan jumlah item_details
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $order->recipient_name,
                'email'      => $order->user->email,
                'phone'      => $order->recipient_phone,
                'shipping_address' => [
                    'first_name'   => $order->recipient_name,
                    'phone'        => $order->recipient_phone,
                    'address'      => $order->shipping_address,
                    'city'         => $order->city,
                    'postal_code'  => $order->postal_code,
                    'country_code' => 'IDN',
                ],
            ],
            'expiry' => [
                'unit'     => 'minutes',
                'duration' => (int) config('midtrans.expiry_minutes'),
            ],
        ];

        return Snap::getSnapToken($params);
    }

    /**
     * Ambil status transaksi langsung dari Midtrans (Status API).
     * Melempar Exception jika transaksi belum ada di Midtrans (HTTP 404).
     */
    public function fetchStatus(string $orderNumber): array
    {
        $response = Transaction::status($orderNumber);

        return json_decode(json_encode($response), true);
    }
}
