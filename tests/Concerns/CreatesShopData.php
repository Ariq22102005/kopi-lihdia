<?php

namespace Tests\Concerns;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

trait CreatesShopData
{
    protected function makeCustomer(array $attrs = []): User
    {
        return User::create(array_merge([
            'name'     => 'Customer ' . Str::random(4),
            'email'    => Str::lower(Str::random(8)) . '@example.com',
            'password' => 'password',
            'role'     => 'customer',
        ], $attrs));
    }

    protected function makeAdmin(): User
    {
        return $this->makeCustomer(['name' => 'Admin Tes', 'role' => 'admin']);
    }

    protected function makeProduct(array $attrs = []): Product
    {
        $category = Category::firstOrCreate(
            ['slug' => 'kategori-tes'],
            ['name' => 'Kategori Tes']
        );

        $name = $attrs['name'] ?? 'Produk Tes ' . Str::random(4);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name'        => $name,
            'slug'        => Str::slug($name) . '-' . Str::lower(Str::random(4)),
            'description' => 'Deskripsi tes',
            'price'       => 20000,
            'stock'       => 10,
            'is_active'   => true,
        ], $attrs));
    }

    /**
     * Membuat order dengan 1 jenis item. Stok produk dianggap sudah dikurangi
     * (seperti kondisi setelah checkout), jadi atur stok produk sesuai kebutuhan.
     * Total = harga x jumlah + ongkir 10.000.
     */
    protected function makeOrder(User $user, ?Product $product = null, array $attrs = [], int $qty = 2): Order
    {
        $product ??= $this->makeProduct(['price' => 20000, 'stock' => 8]);
        $subtotal = $product->price * $qty;

        $order = Order::create(array_merge([
            'user_id'          => $user->id,
            'order_number'     => 'KL-TEST-' . strtoupper(Str::random(6)),
            'recipient_name'   => $user->name,
            'recipient_phone'  => '081234567890',
            'shipping_address' => 'Jl. Contoh No. 1',
            'city'             => 'Jakarta',
            'postal_code'      => '12345',
            'subtotal'         => $subtotal,
            'shipping_cost'    => 10000,
            'total'            => $subtotal + 10000,
            'payment_status'   => Order::PAYMENT_PENDING,
            'order_status'     => Order::STATUS_PENDING,
        ], $attrs));

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'price'        => $product->price,
            'quantity'     => $qty,
            'subtotal'     => $subtotal,
        ]);

        return $order;
    }
}
