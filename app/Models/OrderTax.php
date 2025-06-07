<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTax extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tax_id',
        'name',
        'rate',
        'amount',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'amount' => 'decimal:4',
    ];

    /**
     * Get the order that owns this tax.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the tax definition.
     */
    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    /**
     * Get formatted rate.
     */
    public function getFormattedRateAttribute(): string
    {
        return $this->rate . '%';
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }
} 