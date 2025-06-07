<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderCoupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'coupon_id',
        'code',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:4',
    ];

    /**
     * Get the order that owns this coupon.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the coupon that was used.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get formatted discount amount.
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return '$' . number_format($this->discount_amount, 2);
    }
} 