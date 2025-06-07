<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'tax_class_id',
        'status',
        'visibility',
        'type_id',
    ];

    protected $casts = [
        'status' => 'string',
        'visibility' => 'string',
        'type_id' => 'string',
    ];

    /**
     * Get the tax class for this product.
     */
    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(ProductTaxClass::class);
    }

    /**
     * Get the inventory for this product.
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get the reviews for this product.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews for this product.
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->approved();
    }

    /**
     * Get average rating for this product.
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->approvedReviews()->avg('rating') ?: 0;
    }

    /**
     * Get total review count.
     */
    public function getReviewCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get rating distribution.
     */
    public function getRatingDistribution(): array
    {
        $distribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->approvedReviews()->where('rating', $i)->count();
        }
        
        return $distribution;
    }

    /**
     * Check if customer can review this product.
     */
    public function canBeReviewedByCustomer(int $customerId): bool
    {
        // Customer must have purchased this product
        return OrderItem::whereHas('order', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId)
                  ->where('payment_status', 'paid');
        })->where('product_id', $this->id)->exists();
    }

    /**
     * Get the categories for this product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')
                    ->withPivot('position')
                    ->withTimestamps()
                    ->orderByPivot('position');
    }

    /**
     * Get the media for this product.
     */
    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }

    /**
     * Get the main image for this product.
     */
    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductMedia::class)->where('is_main', true);
    }

    /**
     * Get varchar attribute values.
     */
    public function varcharValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueVarchar::class);
    }

    /**
     * Get text attribute values.
     */
    public function textValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueText::class);
    }

    /**
     * Get decimal attribute values.
     */
    public function decimalValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueDecimal::class);
    }

    /**
     * Scope a query to only include enabled products.
     */
    public function scopeEnabled($query)
    {
        return $query->where('status', 'enabled');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'sku';
    }
} 