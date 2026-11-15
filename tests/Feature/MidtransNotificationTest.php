<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesShopData;
use Tests\TestCase;

class MidtransNotificationTest extends TestCase
{
    use RefreshDatabase, CreatesShopData;

    private const KEY = 'test-server-key';

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => self::KEY]);
    }

    /** Kirim notifikasi seperti Midtrans, dengan signature yang dihitung benar. */
    private function notify(Order $order, array $overrides = [])
    {
        $payload = array_merge([
            'order_id'           => $order->order_number,
            'status_code'        => '200',
            'gross_amount'       => number_format($order->total, 2, '.', ''),
            'transaction_status' => 'settlement',
            'payment_type'       => 'bank_transfer',
            'fraud_status'       => 'accept',
        ], $overrides);

        $payload['signature_key'] = $overrides['signature_key']
            ?? hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . self::KEY);

        return $this->postJson('/midtrans/notification', $payload);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $order = $this->makeOrder($this->makeCustomer());

        $this->notify($order, ['signature_key' => 'palsu'])->assertForbidden();

        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_valid_settlement_marks_order_paid(): void
    {
        $order = $this->makeOrder($this->makeCustomer());

        $this->notify($order)->assertOk();

        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('bank_transfer', $order->payment_type);
        $this->assertNotNull($order->paid_at);
    }

    public function test_amount_mismatch_is_rejected(): void
    {
        $order = $this->makeOrder($this->makeCustomer());

        // signature valid untuk nominal 1.000, tapi total di database berbeda
        $this->notify($order, ['gross_amount' => '1000.00'])->assertStatus(400);

        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_unknown_order_returns_404(): void
    {
        $order = $this->makeOrder($this->makeCustomer());
        $order->order_number = 'TIDAK-ADA';

        $this->notify($order)->assertNotFound();
    }

    public function test_expire_cancels_order_and_restores_stock(): void
    {
        $product = $this->makeProduct(['price' => 20000, 'stock' => 8]); // sisa setelah 2 dipesan
        $order = $this->makeOrder($this->makeCustomer(), $product, [], 2);

        $this->notify($order, ['transaction_status' => 'expire', 'status_code' => '407'])->assertOk();

        $order->refresh();
        $this->assertSame('expired', $order->payment_status);
        $this->assertSame('cancelled', $order->order_status);
        $this->assertSame(10, (int) $product->fresh()->stock);
    }

    public function test_late_expire_does_not_downgrade_paid_order(): void
    {
        $product = $this->makeProduct(['price' => 20000, 'stock' => 8]);
        $order = $this->makeOrder($this->makeCustomer(), $product, [], 2);

        $this->notify($order)->assertOk(); // settlement
        $this->notify($order, ['transaction_status' => 'expire', 'status_code' => '407'])->assertOk();

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertSame(8, (int) $product->fresh()->stock); // stok tidak dikembalikan
    }

    public function test_duplicate_settlement_is_idempotent(): void
    {
        $order = $this->makeOrder($this->makeCustomer());

        $this->notify($order)->assertOk();
        $firstPaidAt = $order->fresh()->paid_at;

        $this->notify($order)->assertOk();

        $this->assertEquals($firstPaidAt, $order->fresh()->paid_at);
    }
}
