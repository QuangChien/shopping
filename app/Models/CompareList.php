<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CompareList extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'session_id',
    ];

    /**
     * Get the customer that owns this compare list.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for this compare list.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CompareListItem::class);
    }

    /**
     * Get the products in this compare list.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'compare_list_items')
                    ->withTimestamps();
    }

    /**
     * Add a product to the compare list.
     */
    public function addProduct(Product $product): bool
    {
        if ($this->hasProduct($product)) {
            return false; // Already exists
        }

        // Limit to 4 products for comparison
        if ($this->items()->count() >= 4) {
            return false; // Limit reached
        }

        $this->items()->create([
            'product_id' => $product->id,
        ]);

        return true;
    }

    /**
     * Remove a product from the compare list.
     */
    public function removeProduct(Product $product): bool
    {
        return $this->items()
                   ->where('product_id', $product->id)
                   ->delete() > 0;
    }

    /**
     * Check if product exists in compare list.
     */
    public function hasProduct(Product $product): bool
    {
        return $this->items()
                   ->where('product_id', $product->id)
                   ->exists();
    }

    /**
     * Clear all items from the compare list.
     */
    public function clearItems(): void
    {
        $this->items()->delete();
    }

    /**
     * Get the total number of items in the compare list.
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Check if compare list is empty.
     */
    public function isEmpty(): bool
    {
        return $this->items_count === 0;
    }

    /**
     * Check if compare list has items.
     */
    public function hasItems(): bool
    {
        return !$this->isEmpty();
    }

    /**
     * Check if compare list is full (4 items).
     */
    public function isFull(): bool
    {
        return $this->items_count >= 4;
    }

    /**
     * Find or create compare list for guest/customer.
     */
    public static function findOrCreateForCustomer(?int $customerId, ?string $sessionId): self
    {
        if ($customerId) {
            return static::firstOrCreate(['customer_id' => $customerId]);
        }

        return static::firstOrCreate(['session_id' => $sessionId]);
    }
}
