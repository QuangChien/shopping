<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'backend_type',
        'frontend_input',
        'frontend_label',
        'is_required',
        'is_unique',
        'is_filterable',
        'is_searchable',
        'is_comparable',
        'is_visible_on_front',
        'sort_order',
        'default_value',
        'frontend_class',
        'note',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'is_filterable' => 'boolean',
        'is_searchable' => 'boolean',
        'is_comparable' => 'boolean',
        'is_visible_on_front' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get varchar values for this attribute.
     */
    public function varcharValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueVarchar::class, 'attribute_id');
    }

    /**
     * Get text values for this attribute.
     */
    public function textValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueText::class, 'attribute_id');
    }

    /**
     * Get int values for this attribute.
     */
    public function intValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueInt::class, 'attribute_id');
    }

    /**
     * Get decimal values for this attribute.
     */
    public function decimalValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueDecimal::class, 'attribute_id');
    }

    /**
     * Get datetime values for this attribute.
     */
    public function datetimeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueDatetime::class, 'attribute_id');
    }

    /**
     * Get boolean values for this attribute.
     */
    public function booleanValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValueBoolean::class, 'attribute_id');
    }

    /**
     * Scope a query to only include required attributes.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope a query to only include filterable attributes.
     */
    public function scopeFilterable($query)
    {
        return $query->where('is_filterable', true);
    }

    /**
     * Scope a query to only include searchable attributes.
     */
    public function scopeSearchable($query)
    {
        return $query->where('is_searchable', true);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'code';
    }
} 