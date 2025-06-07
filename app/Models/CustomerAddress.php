<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'address_type',
        'first_name',
        'last_name',
        'street_address_1',
        'street_address_2',
        'city',
        'state_province',
        'postal_code',
        'country_code',
        'phone',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the customer that owns the address.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->street_address_1;
        
        if ($this->street_address_2) {
            $address .= ', ' . $this->street_address_2;
        }
        
        $address .= ', ' . $this->city . ', ' . $this->state_province . ' ' . $this->postal_code;
        
        if ($this->country_code !== 'US') {
            $address .= ', ' . $this->country_code;
        }
        
        return $address;
    }

    /**
     * Get the full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}