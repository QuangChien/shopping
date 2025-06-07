<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'used_count',
        'customer_usage_limit',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'minimum_amount' => 'decimal:4',
        'maximum_discount' => 'decimal:4',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'customer_usage_limit' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the orders that used this coupon.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'coupon_id');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Scope a query to only include active coupons.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include valid coupons.
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
                    ->where(function ($q) use ($now) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function ($q) use ($now) {
                        $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                    })
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
                    });
    }

    /**
     * Check if the coupon is valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();

        // Check start date
        if ($this->starts_at && $this->starts_at->isAfter($now)) {
            return false;
        }

        // Check expiry date
        if ($this->expires_at && $this->expires_at->isBefore($now)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if the coupon is valid for a specific customer.
     */
    public function isValidForCustomer(int $customerId, float $orderAmount): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check minimum amount
        if ($this->minimum_amount && $orderAmount < $this->minimum_amount) {
            return false;
        }

        // Check customer usage limit
        if ($this->customer_usage_limit) {
            $customerUsage = $this->orders()->where('customer_id', $customerId)->count();
            if ($customerUsage >= $this->customer_usage_limit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given order total.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->minimum_amount && $orderTotal < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = $orderTotal * ($this->value / 100);
        } elseif ($this->type === 'fixed') {
            $discount = $this->value;
        }

        // Apply maximum discount limit
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }

        return min($discount, $orderTotal);
    }

    /**
     * Mark the coupon as used.
     */
    public function markAsUsed(): void
    {
        $this->increment('used_count');
    }

    /**
     * Check if this is a percentage coupon.
     */
    public function isPercentage(): bool
    {
        return $this->type === 'percentage';
    }

    /**
     * Check if this is a fixed amount coupon.
     */
    public function isFixed(): bool
    {
        return $this->type === 'fixed';
    }

    /**
     * Get formatted value.
     */
    public function getFormattedValueAttribute(): string
    {
        return $this->type === 'percentage'
            ? $this->value . '%'
            : '$' . number_format($this->value, 2);
    }

    /**
     * Check if coupon is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if coupon is not yet active.
     */
    public function isNotYetActive(): bool
    {
        return $this->starts_at && $this->starts_at->isFuture();
    }
}
