<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the customer's addresses.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    /**
     * Get the customer's quotes (shopping carts).
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Get the customer's active quote (current shopping cart).
     */
    public function activeQuote(): HasOne
    {
        return $this->hasOne(Quote::class)->where('is_active', true)->latest();
    }

    /**
     * Get the customer's orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the customer's default address.
     */
    public function defaultAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class)->where('is_default', true);
    }

    /**
     * Get or create an active quote for the customer.
     */
    public function getOrCreateActiveQuote(): Quote
    {
        $quote = $this->activeQuote;
        
        if (!$quote) {
            $quote = $this->quotes()->create([
                'is_active' => true,
                'expires_at' => now()->addDays(30),
            ]);
        }
        
        return $quote;
    }

    /**
     * Get the customer's reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the customer's wishlists.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the customer's default wishlist.
     */
    public function defaultWishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class)->where('is_default', true);
    }

    /**
     * Get the customer's compare lists.
     */
    public function compareLists(): HasMany
    {
        return $this->hasMany(CompareList::class);
    }

    /**
     * Get or create default wishlist for the customer.
     */
    public function getOrCreateDefaultWishlist(): Wishlist
    {
        $wishlist = $this->defaultWishlist;
        
        if (!$wishlist) {
            $wishlist = $this->wishlists()->create([
                'name' => 'My Wishlist',
                'is_default' => true,
                'is_public' => false,
            ]);
        }
        
        return $wishlist;
    }

    /**
     * Get or create compare list for the customer.
     */
    public function getOrCreateCompareList(): CompareList
    {
        $compareList = $this->compareLists()->first();
        
        if (!$compareList) {
            $compareList = $this->compareLists()->create();
        }
        
        return $compareList;
    }
} 