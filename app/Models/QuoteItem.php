<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'row_total',
        'product_options',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:4',
        'row_total' => 'decimal:4',
        'product_options' => 'array',
    ];

    /**
     * Get the quote that owns this item.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
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