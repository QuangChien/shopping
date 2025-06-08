<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_sku',
        'product_name',
        'quantity',
        'price',
        'row_total',
        'product_options',
    ];

    protected $casts = [
        'price' => 'float',
        'row_total' => 'float',
        'product_options' => 'array',
    ];

    /**
     * Automatically calculate row_total when setting quantity or price
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Tự động tính tổng tiền dòng nếu có quantity và price
            if (isset($item->quantity) && isset($item->price) && !isset($item->row_total)) {
                $item->row_total = $item->quantity * $item->price;
            }
        });
    }

    /**
     * Quan hệ với đơn hàng
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product for this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get formatted row total.
     */
    public function getFormattedRowTotalAttribute(): string
    {
        return '$' . number_format($this->row_total, 2);
    }

    /**
     * Calculate row total based on quantity and price.
     */
    public function calculateRowTotal(): void
    {
        $this->row_total = $this->quantity * $this->price;
        $this->save();
    }

    /**
     * Get product option value.
     */
    public function getProductOption(string $key, $default = null)
    {
        return $this->product_options[$key] ?? $default;
    }

    /**
     * Check if item has specific product option.
     */
    public function hasProductOption(string $key): bool
    {
        return isset($this->product_options[$key]);
    }
}
