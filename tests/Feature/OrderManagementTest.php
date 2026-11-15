<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesShopData;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase, CreatesShopData;

    public function test_admin_cannot_change_status_of_unpaid_order(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['order_status' => 'processing'])
            ->assertSessionHas('error');

        $this->assertSame('pending', $order->fresh()->order_status);
    }

    public function test_admin_can_move_paid_order_forward_step_by_step(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer(), null, ['payment_status' => 'paid']);

        foreach (['processing', 'shipped', 'completed'] as $status) {
            $this->actingAs($admin)
                ->patch(route('admin.orders.status', $order), ['order_status' => $status])
                ->assertSessionHas('success');

            $this->assertSame($status, $order->fresh()->order_status);
        }
    }

    public function test_admin_cannot_skip_status_steps(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer(), null, ['payment_status' => 'paid']);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['order_status' => 'completed'])
            ->assertSessionHasErrors('order_status');

        $this->assertSame('pending', $order->fresh()->order_status);
    }

    public function test_admin_cannot_change_payment_status(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer(), null, ['payment_status' => 'paid']);

        // payment_status ikut dikirim sebagai upaya manipulasi, harus diabaikan
        $this->actingAs($admin)->patch(route('admin.orders.status', $order), [
            'order_status'   => 'processing',
            'payment_status' => 'failed',
        ]);

        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_admin_cannot_mark_unpaid_order_as_paid(): void
    {
        $admin = $this->makeAdmin();
        $order = $this->makeOrder($this->makeCustomer());

        $this->actingAs($admin)->patch(route('admin.orders.status', $order), [
            'order_status'   => 'processing',
            'payment_status' => 'paid',
        ]);

        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_customer_cannot_update_order_status(): void
    {
        $customer = $this->makeCustomer();
        $order = $this->makeOrder($customer, null, ['payment_status' => 'paid']);

        $this->actingAs($customer)
            ->patch(route('admin.orders.status', $order), ['order_status' => 'processing'])
            ->assertForbidden();
    }

    public function test_customer_cannot_view_another_customers_order(): void
    {
        $owner = $this->makeCustomer();
        $other = $this->makeCustomer();
        $order = $this->makeOrder($owner);

        $this->actingAs($other)->get(route('orders.show', $order))->assertForbidden();
        $this->actingAs($other)->get(route('orders.invoice', $order))->assertForbidden();
        $this->actingAs($owner)->get(route('orders.show', $order))->assertOk();
    }

    public function test_order_history_only_lists_own_orders(): void
    {
        $owner = $this->makeCustomer();
        $other = $this->makeCustomer();
        $mine = $this->makeOrder($owner);
        $theirs = $this->makeOrder($other);

        $this->actingAs($owner)->get(route('orders.index'))
            ->assertSee($mine->order_number)
            ->assertDontSee($theirs->order_number);
    }
}
