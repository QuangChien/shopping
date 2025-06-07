<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'type',
        'country',
        'state',
        'zip_code',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Scope a query to only include active taxes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query for specific location.
     */
    public function scopeForLocation($query, $country, $state = null, $zipCode = null)
    {
        return $query->where(function ($q) use ($country, $state, $zipCode) {
            $q->where('country', $country)
              ->where(function ($subQ) use ($state) {
                  $subQ->whereNull('state')->orWhere('state', $state);
              })
              ->where(function ($subQ) use ($zipCode) {
                  $subQ->whereNull('zip_code')->orWhere('zip_code', $zipCode);
              });
        });
    }

    /**
     * Calculate tax amount for a given amount.
     */
    public function calculateTax(float $amount): float
    {
        if ($this->type === 'percentage') {
            return $amount * ($this->rate / 100);
        }

        return $this->rate; // Fixed amount
    }

    /**
     * Get formatted rate.
     */
    public function getFormattedRateAttribute(): string
    {
        return $this->type === 'percentage' 
            ? $this->rate . '%' 
            : '$' . number_format($this->rate, 2);
    }

    /**
     * Check if this is a percentage tax.
     */
    public function isPercentage(): bool
    {
        return $this->type === 'percentage';
    }

    /**
     * Check if this is a fixed amount tax.
     */
    public function isFixed(): bool
    {
        return $this->type === 'fixed';
    }
} 