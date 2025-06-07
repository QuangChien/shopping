<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'is_default',
        'is_public',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get the customer that owns this wishlist.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for this wishlist.
     */
    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Get the products in this wishlist.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'wishlist_items')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include default wishlists.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include public wishlists.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Add a product to the wishlist.
     */
    public function addProduct(Product $product): bool
    {
        if ($this->hasProduct($product)) {
            return false; // Already exists
        }

        $this->items()->create([
            'product_id' => $product->id,
        ]);

        return true;
    }

    /**
     * Remove a product from the wishlist.
     */
    public function removeProduct(Product $product): bool
    {
        return $this->items()
                   ->where('product_id', $product->id)
                   ->delete() > 0;
    }

    /**
     * Check if product exists in wishlist.
     */
    public function hasProduct(Product $product): bool
    {
        return $this->items()
                   ->where('product_id', $product->id)
                   ->exists();
    }

    /**
     * Clear all items from the wishlist.
     */
    public function clearItems(): void
    {
        $this->items()->delete();
    }

    /**
     * Get the total number of items in the wishlist.
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Check if wishlist is empty.
     */
    public function isEmpty(): bool
    {
        return $this->items_count === 0;
    }

    /**
     * Check if wishlist has items.
     */
    public function hasItems(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Set as default wishlist for the customer.
     */
    public function setAsDefault(): void
    {
        // Remove default from other wishlists
        $this->customer->wishlists()
                       ->where('id', '!=', $this->id)
                       ->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
    }

    /**
     * Toggle public/private status.
     */
    public function toggleVisibility(): void
    {
        $this->update(['is_public' => !$this->is_public]);
    }

    /**
     * Get display name for the wishlist.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: 'My Wishlist';
    }
} 