<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'status_id',
        'payment_method_id',
        'shipping_method_id',
        'billing_address',
        'shipping_address',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total',
        'payment_status',
        'shipping_status',
        'notes',
        'shipped_at',
        'delivered_at'
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'subtotal' => 'float',
        'tax_amount' => 'float',
        'shipping_amount' => 'float',
        'discount_amount' => 'float',
        'total' => 'float',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Automatically generate order code when creating new order
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            // If there is no order code, generate one automatically
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    /**
     * Customer Relations
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relationship with order status
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    /**
     * Relationship with payment method
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Relationship with shipping method
     */
    public function shippingMethod(): BelongsTo
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    /**
     * Relationship to order items
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if the order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        $notCancellableStatuses = ['completed', 'delivered', 'shipped'];
        return !in_array($this->status->name, $notCancellableStatuses);
    }

    /**
     * Check if the order has been paid
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Get the quote that created this order.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the coupon used.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * Create an order from a quote.
     */
    public static function createFromQuote(Quote $quote, array $orderData = []): self
    {
        return $quote->convertToOrder($orderData);
    }

    /**
     * Update the order status.
     */
    public function updateStatus(string $statusName): bool
    {
        $status = OrderStatus::where('name', $statusName)->first();

        if (!$status) {
            return false;
        }

        $this->update(['status_id' => $status->id]);

        // Handle status-specific actions
        $this->handleStatusChange($statusName);

        return true;
    }

    /**
     * Handle actions when status changes.
     */
    protected function handleStatusChange(string $statusName): void
    {
        switch ($statusName) {
            case 'shipped':
                $this->update(['shipped_at' => now()]);
                $this->reserveInventory();
                break;

            case 'delivered':
                $this->update(['delivered_at' => now()]);
                $this->reduceInventory();
                break;

            case 'cancelled':
                $this->releaseInventory();
                break;
        }
    }

    /**
     * Reserve inventory for order items.
     */
    public function reserveInventory(): void
    {
        foreach ($this->items as $item) {
            if ($item->product && $item->product->inventory) {
                $item->product->inventory->reserve($item->quantity);
            }
        }
    }

    /**
     * Release reserved inventory.
     */
    public function releaseInventory(): void
    {
        foreach ($this->items as $item) {
            if ($item->product && $item->product->inventory) {
                $item->product->inventory->release($item->quantity);
            }
        }
    }

    /**
     * Reduce actual inventory (when shipped).
     */
    public function reduceInventory(): void
    {
        foreach ($this->items as $item) {
            if ($item->product && $item->product->inventory) {
                $item->product->inventory->reduce($item->quantity);
            }
        }
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    /**
     * Check if order is shipped.
     */
    public function isShipped(): bool
    {
        return $this->shipping_status === 'shipped' || !is_null($this->shipped_at);
    }

    /**
     * Check if order is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->shipping_status === 'delivered' || !is_null($this->delivered_at);
    }

    /**
     * Get formatted grand total.
     */
    public function getFormattedGrandTotalAttribute(): string
    {
        return '$' . number_format($this->grand_total, 2);
    }

    /**
     * Get total items count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Scope orders by status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->whereHas('status', function ($q) use ($status) {
            $q->where('name', $status);
        });
    }

    /**
     * Scope orders by payment status.
     */
    public function scopeByPaymentStatus($query, string $paymentStatus)
    {
        return $query->where('payment_status', $paymentStatus);
    }

    /**
     * Scope recent orders.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
