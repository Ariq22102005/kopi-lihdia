<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Pesanan contoh untuk demo: menampilkan semua kondisi status.
 * Harus dijalankan setelah UserSeeder dan CatalogSeeder.
 */
class OrderSeeder extends Seeder
{
    private const SHIPPING = 10000;

    public function run(): void
    {
        $budi = User::where('email', 'budi@example.com')->firstOrFail();
        $siti = User::where('email', 'siti@example.com')->firstOrFail();

        $specs = [
            // 1. Menunggu pembayaran
            [$budi, 'KL-DEMO-0001', 'pending', 'pending', null, now()->subHour(),
                [['Es Kopi Susu Lihdia', 2], ['Croissant Butter', 1]]],
            // 2. Dibayar dan sedang dikirim
            [$budi, 'KL-DEMO-0002', 'paid', 'shipped', 'bank_transfer', now()->subDays(2),
                [['Americano', 1], ['Cappuccino', 2]]],
            // 3. Dibayar dan sedang diproses
            [$siti, 'KL-DEMO-0003', 'paid', 'processing', 'qris', now()->subDay(),
                [['Matcha Latte', 2]]],
            // 4. Kedaluwarsa (stok dikembalikan, tidak dikurangi)
            [$siti, 'KL-DEMO-0004', 'expired', 'cancelled', null, now()->subDays(3),
                [['Es Kopi Susu Pandan', 1]]],
            // 5. Dibayar dan selesai
            [$budi, 'KL-DEMO-0005', 'paid', 'completed', 'credit_card', now()->subDays(5),
                [['Teh Tarik Lihdia', 3]]],
        ];

        foreach ($specs as [$user, $number, $payStatus, $orderStatus, $payType, $when, $lines]) {
            $this->createOrder($user, $number, $payStatus, $orderStatus, $payType, $when, $lines);
        }
    }

    private function createOrder($user, $number, $payStatus, $orderStatus, $payType, $when, array $lines): void
    {
        $subtotal = 0;
        $rows = [];

        foreach ($lines as [$name, $qty]) {
            $product = Product::where('name', $name)->firstOrFail();
            $lineTotal = $product->price * $qty;
            $subtotal += $lineTotal;
            $rows[] = [$product, $qty, $lineTotal];
        }

        $order = Order::unguarded(fn () => Order::create([
            'user_id'          => $user->id,
            'order_number'     => $number,
            'recipient_name'   => $user->name,
            'recipient_phone'  => $user->phone ?? '081200000000',
            'shipping_address' => $user->address ?? 'Jl. Contoh No. 1',
            'city'             => 'Jakarta',
            'postal_code'      => '10110',
            'subtotal'         => $subtotal,
            'shipping_cost'    => self::SHIPPING,
            'total'            => $subtotal + self::SHIPPING,
            'payment_status'   => $payStatus,
            'order_status'     => $orderStatus,
            'payment_type'     => $payType,
            'paid_at'          => $payStatus === 'paid' ? $when->copy()->addMinutes(5) : null,
            'created_at'       => $when,
            'updated_at'       => $when,
        ]));

        foreach ($rows as [$product, $qty, $lineTotal]) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'price'        => $product->price,
                'quantity'     => $qty,
                'subtotal'     => $lineTotal,
            ]);

            // Stok hanya berkurang untuk pesanan yang masih aktif (pending/paid)
            if (in_array($payStatus, ['pending', 'paid'], true)) {
                $product->decrement('stock', $qty);
            }
        }
    }
}
