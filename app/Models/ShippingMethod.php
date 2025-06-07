<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'free_shipping_threshold',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'free_shipping_threshold' => 'decimal:4',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the orders using this shipping method.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_method_id');
    }

    /**
     * Scope a query to only include active shipping methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }

    /**
     * Calculate shipping cost for a given total.
     */
    public function calculateCost(float $orderTotal): float
    {
        if ($this->free_shipping_threshold && $orderTotal >= $this->free_shipping_threshold) {
            return 0.00;
        }

        return $this->price;
    }

    /**
     * Check if free shipping applies for given total.
     */
    public function isFreeShipping(float $orderTotal): bool
    {
        return $this->free_shipping_threshold && $orderTotal >= $this->free_shipping_threshold;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }
} 