<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'guest_email',
        'is_active',
        'items_count',
        'items_qty',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'coupon_code',
        'grand_total',
        'base_currency_code',
        'quote_currency_code',
        'shipping_address',
        'billing_address',
        'payment_method',
        'shipping_method',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'items_count' => 'integer',
        'items_qty' => 'decimal:2',
        'subtotal' => 'decimal:4',
        'tax_amount' => 'decimal:4',
        'shipping_amount' => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'grand_total' => 'decimal:4',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the customer that owns this quote.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the items for this quote.
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    /**
     * Scope a query to only include active quotes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include expired quotes.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', Carbon::now());
    }

    /**
     * Add an item to the quote.
     */
    public function addItem(Product $product, int $quantity = 1, array $options = []): QuoteItem
    {
        // Check if item already exists
        $existingItem = $this->items()
            ->where('product_id', $product->id)
            ->where('product_options', json_encode($options))
            ->first();

        if ($existingItem) {
            $existingItem->update([
                'quantity' => $existingItem->quantity + $quantity,
                'row_total' => ($existingItem->quantity + $quantity) * $existingItem->price,
            ]);
            $item = $existingItem;
        } else {
            $item = $this->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $quantity,
                'price' => $product->price,
                'row_total' => $product->price * $quantity,
                'product_options' => $options,
            ]);
        }

        $this->updateTotals();
        return $item;
    }

    /**
     * Remove an item from the quote.
     */
    public function removeItem(int $itemId): bool
    {
        $item = $this->items()->find($itemId);
        
        if ($item) {
            $item->delete();
            $this->updateTotals();
            return true;
        }

        return false;
    }

    /**
     * Update item quantity.
     */
    public function updateItemQuantity(int $itemId, int $quantity): bool
    {
        $item = $this->items()->find($itemId);
        
        if ($item) {
            if ($quantity <= 0) {
                return $this->removeItem($itemId);
            }

            $item->update([
                'quantity' => $quantity,
                'row_total' => $quantity * $item->price,
            ]);
            
            $this->updateTotals();
            return true;
        }

        return false;
    }

    /**
     * Clear all items from the quote.
     */
    public function clearItems(): void
    {
        $this->items()->delete();
        $this->updateTotals();
    }

    /**
     * Update quote totals.
     */
    public function updateTotals(): void
    {
        $items = $this->items;
        
        $this->update([
            'items_count' => $items->count(),
            'items_qty' => $items->sum('quantity'),
            'subtotal' => $items->sum('row_total'),
            'grand_total' => $items->sum('row_total') + $this->tax_amount + $this->shipping_amount - $this->discount_amount,
        ]);
    }

    /**
     * Apply coupon to the quote.
     */
    public function applyCoupon(string $couponCode): bool
    {
        $coupon = Coupon::where('code', $couponCode)->valid()->first();
        
        if (!$coupon) {
            return false;
        }

        $discount = $coupon->calculateDiscount($this->subtotal);
        
        $this->update([
            'coupon_code' => $couponCode,
            'discount_amount' => $discount,
        ]);
        
        $this->updateTotals();
        return true;
    }

    /**
     * Remove coupon from the quote.
     */
    public function removeCoupon(): void
    {
        $this->update([
            'coupon_code' => null,
            'discount_amount' => 0,
        ]);
        
        $this->updateTotals();
    }

    /**
     * Check if quote is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if quote has items.
     */
    public function hasItems(): bool
    {
        return $this->items_count > 0;
    }

    /**
     * Get the total weight of all items.
     */
    public function getTotalWeight(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * ($item->product->weight ?? 0);
        });
    }

    /**
     * Convert quote to order.
     */
    public function convertToOrder(array $orderData = []): Order
    {
        $order = Order::create(array_merge([
            'customer_id' => $this->customer_id,
            'quote_id' => $this->id,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'shipping_amount' => $this->shipping_amount,
            'discount_amount' => $this->discount_amount,
            'grand_total' => $this->grand_total,
            'shipping_address' => $this->shipping_address,
            'billing_address' => $this->billing_address,
            'payment_method' => $this->payment_method,
            'shipping_method' => $this->shipping_method,
            'coupon_code' => $this->coupon_code,
        ], $orderData));

        // Copy items to order
        foreach ($this->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'row_total' => $item->row_total,
                'product_options' => $item->product_options,
            ]);
        }

        // Deactivate quote
        $this->update(['is_active' => false]);

        return $order;
    }
} 