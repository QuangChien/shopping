<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductTaxClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the products for this tax class.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'tax_class_id');
    }

    /**
     * Scope a query to only include active tax classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
} 