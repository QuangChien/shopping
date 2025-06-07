<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WishlistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'wishlist_id',
        'product_id',
    ];

    /**
     * Get the wishlist that owns this item.
     */
    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
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
