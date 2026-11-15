<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesShopData;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase, CreatesShopData;

    private function checkoutData(array $extra = []): array
    {
        return array_merge([
            'recipient_name'   => 'Budi Santoso',
            'recipient_phone'  => '081234567890',
            'shipping_address' => 'Jl. Merdeka No. 10',
            'city'             => 'Jakarta',
            'postal_code'      => '10110',
        ], $extra);
    }

    public function test_total_is_calculated_by_server_and_ignores_tampered_input(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct(['price' => 25000, 'stock' => 10]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user)->post(route('checkout.store'), $this->checkoutData([
            'total'    => 1,      // percobaan manipulasi dari browser
            'subtotal' => 1,
            'price'    => 1,
        ]));

        $order = Order::firstOrFail();

        // 25.000 x 2 + ongkir 10.000
        $this->assertSame(50000, (int) $order->subtotal);
        $this->assertSame(60000, (int) $order->total);
        $this->assertSame('pending', $order->payment_status);
    }

    public function test_checkout_creates_snapshot_reduces_stock_and_clears_cart(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct(['name' => 'Es Kopi Tes', 'price' => 18000, 'stock' => 10]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 3]);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutData())
            ->assertRedirect(route('orders.show', Order::firstOrFail()));

        $item = Order::first()->items->first();
        $this->assertSame('Es Kopi Tes', $item->product_name);
        $this->assertSame(18000, (int) $item->price);
        $this->assertSame(3, (int) $item->quantity);

        $this->assertSame(7, (int) $product->fresh()->stock);
        $this->assertSame(0, CartItem::where('user_id', $user->id)->count());
    }

    public function test_checkout_fails_when_stock_is_insufficient(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct(['stock' => 2]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 3]);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutData())
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, Order::count());
        $this->assertSame(2, (int) $product->fresh()->stock);
    }

    public function test_checkout_validates_required_fields(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct();
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($user)
            ->post(route('checkout.store'), $this->checkoutData(['postal_code' => '12', 'recipient_phone' => 'abc']))
            ->assertSessionHasErrors(['postal_code', 'recipient_phone']);

        $this->assertSame(0, Order::count());
    }

    public function test_empty_cart_cannot_checkout(): void
    {
        $user = $this->makeCustomer();

        $this->actingAs($user)->get(route('checkout.index'))->assertRedirect(route('cart.index'));
    }
}
