<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompareListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'compare_list_id',
        'product_id',
    ];

    /**
     * Get the compare list that owns this item.
     */
    public function compareList(): BelongsTo
    {
        return $this->belongsTo(CompareList::class);
    }

    /**
     * Get the product for this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if product is still available.
     */
    public function isProductAvailable(): bool
    {
        return $this->product && $this->product->status === 'enabled';
    }

    /**
     * Get product price.
     */
    public function getProductPrice(): ?float
    {
        return $this->product?->price;
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->product && $this->product->inventory?->inStock();
    }
}
