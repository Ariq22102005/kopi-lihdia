<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->restrictOnDelete();
        $table->string('order_number')->unique();

        // data penerima
        $table->string('recipient_name');
        $table->string('recipient_phone', 20);
        $table->text('shipping_address');
        $table->string('city');
        $table->string('postal_code', 10);
        $table->text('notes')->nullable();

        // uang (rupiah, integer)
        $table->unsignedBigInteger('subtotal');
        $table->unsignedBigInteger('shipping_cost')->default(0);
        $table->unsignedBigInteger('total');

        // status
        $table->string('payment_status', 20)->default('pending');
        $table->string('order_status', 20)->default('pending');

        // midtrans
        $table->string('snap_token')->nullable();
        $table->string('payment_type')->nullable();
        $table->timestamp('paid_at')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
