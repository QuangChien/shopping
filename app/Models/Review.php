<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'rating',
        'title',
        'content',
        'status',
        'helpful_count',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Get the product that owns this review.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer who wrote this review.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order this review relates to.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope a query to only include approved reviews.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include pending reviews.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include rejected reviews.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Scope reviews by rating.
     */
    public function scopeByRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Check if review is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if review is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if review is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Approve the review.
     */
    public function approve(): void
    {
        $this->update(['status' => self::STATUS_APPROVED]);
    }

    /**
     * Reject the review.
     */
    public function reject(): void
    {
        $this->update(['status' => self::STATUS_REJECTED]);
    }

    /**
     * Mark as pending.
     */
    public function markPending(): void
    {
        $this->update(['status' => self::STATUS_PENDING]);
    }

    /**
     * Increment helpful count.
     */
    public function markHelpful(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Get formatted rating (stars).
     */
    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get customer display name.
     */
    public function getCustomerDisplayNameAttribute(): string
    {
        if ($this->customer) {
            return $this->customer->first_name . ' ' . substr($this->customer->last_name, 0, 1) . '.';
        }
        
        return 'Anonymous';
    }

    /**
     * Check if review has helpful votes.
     */
    public function hasHelpfulVotes(): bool
    {
        return $this->helpful_count > 0;
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_REJECTED => 'badge-danger',
            default => 'badge-secondary'
        };
    }
} 