<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'stripe_id',
        'plan',
        'status',
        'amount',
        'currency',
        'interval',
        'metadata',
        'current_period_start',
        'current_period_end',
        'ends_at',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'ends_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the vendor that owns the subscription.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Determine if the subscription is active.
     */
    public function active(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Determine if the subscription is cancelled.
     */
    public function cancelled(): bool
    {
        return $this->status === 'canceled';
    }

    /**
     * Determine if the subscription is on trial.
     */
    public function onTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Determine if the subscription is within its grace period after cancellation.
     */
    public function onGracePeriod(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }
}