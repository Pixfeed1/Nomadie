<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payable_type',
        'payable_id',
        'payment_id',
        'payment_method',
        'currency',
        'amount',
        'status',
        'metadata',
        'paid_at',
        'refunded_at',
        'refund_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'metadata' => 'array',
        'amount' => 'integer',
    ];

    /**
     * Get the payable model (vendor, order, etc.).
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Determine if the payment was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'succeeded';
    }

    /**
     * Determine if the payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Determine if the payment failed.
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'canceled']);
    }

    /**
     * Determine if the payment was refunded.
     */
    public function isRefunded(): bool
    {
        return !is_null($this->refunded_at);
    }

    /**
     * Get the amount in euros (convert from cents).
     */
    public function getAmountInEurosAttribute(): float
    {
        return $this->amount / 100;
    }

    /**
     * Format the amount for display.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount / 100, 2) . ' €';
    }
}