<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'reserved_quantity',
        'min_quantity',
        'max_quantity',
        'is_in_stock',
        'manage_stock',
        'backorders',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_in_stock' => 'boolean',
        'manage_stock' => 'boolean',
        'updated_at' => 'datetime',
    ];

    const UPDATED_AT = 'updated_at';
    const CREATED_AT = null;

    /**
     * Get the product that owns this inventory.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the available quantity (quantity - reserved).
     */
    public function getAvailableQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    /**
     * Check if the product is in stock.
     */
    public function inStock(): bool
    {
        if (!$this->manage_stock) {
            return $this->is_in_stock;
        }

        return $this->available_quantity > 0;
    }

    /**
     * Check if the stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->manage_stock && $this->available_quantity <= $this->min_quantity;
    }

    /**
     * Reserve quantity for an order.
     */
    public function reserve(int $quantity): bool
    {
        if ($this->available_quantity >= $quantity) {
            $this->increment('reserved_quantity', $quantity);
            return true;
        }

        return false;
    }

    /**
     * Release reserved quantity.
     */
    public function release(int $quantity): void
    {
        $this->decrement('reserved_quantity', min($this->reserved_quantity, $quantity));
    }

    /**
     * Reduce actual quantity (when order is shipped).
     */
    public function reduce(int $quantity): void
    {
        $this->decrement('quantity', $quantity);
        $this->decrement('reserved_quantity', min($this->reserved_quantity, $quantity));
        
        // Update stock status
        if ($this->manage_stock && $this->available_quantity <= 0) {
            $this->update(['is_in_stock' => false]);
        }
    }

    /**
     * Add stock.
     */
    public function addStock(int $quantity): void
    {
        $this->increment('quantity', $quantity);
        
        // Update stock status
        if ($this->manage_stock && $this->available_quantity > 0) {
            $this->update(['is_in_stock' => true]);
        }
    }

    /**
     * Scope a query to only include in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where('is_in_stock', true);
    }

    /**
     * Scope a query to only include low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->where('manage_stock', true)
                    ->whereColumn('quantity', '<=', 'min_quantity');
    }
} 