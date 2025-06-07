<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get setting value by key.
     */
    public static function getValue(string $key, $default = null)
    {
        $cacheKey = 'setting_' . $key;
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return static::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Set setting value by key.
     */
    public static function setValue(string $key, $value, string $type = 'string', string $group = 'general'): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        
        $setting->fill([
            'value' => is_array($value) ? json_encode($value) : $value,
            'type' => $type,
            'group' => $group,
        ]);
        
        $setting->save();
        
        // Clear cache
        Cache::forget('setting_' . $key);
        Cache::forget('settings_group_' . $group);
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        $cacheKey = 'settings_group_' . $group;
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $settings = static::where('group', $group)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = static::castValue($setting->value, $setting->type);
            }
            
            return $result;
        });
    }

    /**
     * Get all public settings.
     */
    public static function getPublicSettings(): array
    {
        return Cache::remember('public_settings', 3600, function () {
            $settings = static::where('is_public', true)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = static::castValue($setting->value, $setting->type);
            }
            
            return $result;
        });
    }

    /**
     * Cast value to appropriate type.
     */
    protected static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'string':
            default:
                return $value;
        }
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        $groups = static::distinct()->pluck('group');
        
        foreach ($groups as $group) {
            Cache::forget('settings_group_' . $group);
        }
        
        Cache::forget('public_settings');
        
        // Clear individual setting caches
        $keys = static::pluck('key');
        foreach ($keys as $key) {
            Cache::forget('setting_' . $key);
        }
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'key';
    }

    /**
     * Scope a query to only include public settings.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query for a specific group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Boot method to clear cache when model is updated.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saved(function ($setting) {
            Cache::forget('setting_' . $setting->key);
            Cache::forget('settings_group_' . $setting->group);
            Cache::forget('public_settings');
        });
        
        static::deleted(function ($setting) {
            Cache::forget('setting_' . $setting->key);
            Cache::forget('settings_group_' . $setting->group);
            Cache::forget('public_settings');
        });
    }

    /**
     * Get typed value attribute.
     */
    public function getTypedValueAttribute()
    {
        return static::castValue($this->value, $this->type);
    }

    /**
     * Common setting shortcuts.
     */
    public static function siteName(): string
    {
        return static::getValue('site_name', 'Ecommerce Store');
    }

    public static function siteDescription(): string
    {
        return static::getValue('site_description', '');
    }

    public static function siteLogo(): string
    {
        return static::getValue('site_logo', '');
    }

    public static function currency(): string
    {
        return static::getValue('currency', 'USD');
    }

    public static function currencySymbol(): string
    {
        return static::getValue('currency_symbol', '$');
    }

    public static function timezone(): string
    {
        return static::getValue('timezone', 'UTC');
    }

    public static function defaultLanguage(): string
    {
        return static::getValue('default_language', 'en');
    }

    public static function taxRate(): float
    {
        return (float) static::getValue('tax_rate', 0);
    }

    public static function shippingRate(): float
    {
        return (float) static::getValue('shipping_rate', 0);
    }

    public static function freeShippingThreshold(): float
    {
        return (float) static::getValue('free_shipping_threshold', 0);
    }
} 