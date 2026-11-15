<?php

namespace Tests\Feature;

use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesShopData;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase, CreatesShopData;

    public function test_customer_can_add_product_within_stock(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct(['stock' => 5]);

        $this->actingAs($user)->post(route('cart.store', $product), ['quantity' => 2]);

        $this->assertSame(2, (int) CartItem::where('user_id', $user->id)->value('quantity'));
    }

    public function test_cannot_add_more_than_stock(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct(['stock' => 5]);

        $this->actingAs($user)
            ->post(route('cart.store', $product), ['quantity' => 6])
            ->assertSessionHas('error');

        $this->assertSame(0, CartItem::count());
    }

    public function test_repeated_add_cannot_exceed_stock(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct(['stock' => 3]);

        $this->actingAs($user)->post(route('cart.store', $product), ['quantity' => 2]);
        $this->actingAs($user)->post(route('cart.store', $product), ['quantity' => 2])
            ->assertSessionHas('error');

        $this->assertSame(2, (int) CartItem::where('user_id', $user->id)->value('quantity'));
    }

    public function test_cannot_add_out_of_stock_product(): void
    {
        $user = $this->makeCustomer();
        $product = $this->makeProduct(['stock' => 0]);

        $this->actingAs($user)
            ->post(route('cart.store', $product))
            ->assertSessionHas('error');

        $this->assertSame(0, CartItem::count());
    }

    public function test_user_cannot_modify_another_users_cart(): void
    {
        $owner = $this->makeCustomer();
        $other = $this->makeCustomer();
        $product = $this->makeProduct(['stock' => 10]);
        $item = CartItem::create(['user_id' => $owner->id, 'product_id' => $product->id, 'quantity' => 1]);

        $this->actingAs($other)->patch(route('cart.update', $item), ['quantity' => 3])->assertForbidden();
        $this->actingAs($other)->delete(route('cart.destroy', $item))->assertForbidden();

        $this->assertSame(1, (int) $item->fresh()->quantity);
    }

    public function test_admin_cannot_shop(): void
    {
        $admin = $this->makeAdmin();
        $product = $this->makeProduct();

        $this->actingAs($admin)->post(route('cart.store', $product))->assertForbidden();
    }
}
