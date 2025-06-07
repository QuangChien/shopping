<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'color',
        'description',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the orders for this status.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'status_id');
    }

    /**
     * Scope a query to only include the default status.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'name';
    }

    /**
     * Get the color with # prefix if not already present.
     */
    public function getColorAttribute($value): string
    {
        return str_starts_with($value, '#') ? $value : '#' . $value;
    }
} 