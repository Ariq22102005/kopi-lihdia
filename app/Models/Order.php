<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Status pembayaran (HANYA diubah lewat notifikasi Midtrans)
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID    = 'paid';
    public const PAYMENT_EXPIRED = 'expired';
    public const PAYMENT_FAILED  = 'failed';

    // Status pemrosesan (diubah admin)
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_CANCELLED  = 'cancelled';

    protected $fillable = [
        'user_id', 'order_number',
        'recipient_name', 'recipient_phone', 'shipping_address',
        'city', 'postal_code', 'notes',
        'subtotal', 'shipping_cost', 'total',
        'payment_status', 'order_status',
        'snap_token', 'payment_type', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}