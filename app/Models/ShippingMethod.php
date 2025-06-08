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
        'is_active',
        'sort_order',
        'calculation_method',
        'calculation_rules',
    ];

    protected $casts = [
        'price' => 'float',
        'is_active' => 'boolean',
        'calculation_rules' => 'array',
    ];

    /**
     * Quan hệ với đơn hàng
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Tính giá vận chuyển dựa trên đơn hàng
     */
    public function calculatePrice(array $orderData): float
    {
        // Mặc định, trả về giá cố định
        if ($this->calculation_method === 'fixed') {
            return $this->price;
        }

        // Tính dựa trên trọng lượng
        if ($this->calculation_method === 'weight') {
            $totalWeight = 0;
            if (isset($orderData['items'])) {
                foreach ($orderData['items'] as $item) {
                    $weight = $item['weight'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;
                    $totalWeight += $weight * $quantity;
                }
            }

            // Tìm khoảng trọng lượng phù hợp
            $rules = $this->calculation_rules['weight_ranges'] ?? [];
            foreach ($rules as $rule) {
                if ($totalWeight >= ($rule['from'] ?? 0) && $totalWeight <= ($rule['to'] ?? PHP_FLOAT_MAX)) {
                    return $rule['price'] ?? $this->price;
                }
            }
        }

        return $this->price;
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