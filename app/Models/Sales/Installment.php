<?php

namespace App\Models\Sales;

use App\Models\MainCore\PaymentTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Installment extends Model
{
    protected $fillable = [
        'installmentable_type',
        'installmentable_id',
        'installment_number',
        'total_amount',
        'installment_count',
        'installment_amount',
        'start_date',
        'frequency',
        'interest_rate',
        'status',
        'payment_schedule',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'installment_amount' => 'decimal:2',
        'start_date' => 'date',
        'interest_rate' => 'decimal:2',
        'payment_schedule' => 'array',
    ];

    /**
     * Get the parent model (Order or Invoice)
     */
    public function installmentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all installment payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class);
    }

    /**
     * Get paid amount
     */
    public function getPaidAmountAttribute(): float
    {
        return $this->payments()->where('status', 'paid')->sum('paid_amount');
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_amount - $this->paid_amount;
    }

    /**
     * Get overdue payments count
     */
    public function getOverduePaymentsCountAttribute(): int
    {
        return $this->payments()
            ->where('status', 'overdue')
            ->orWhere(function ($query) {
                $query->where('status', 'pending')
                    ->where('due_date', '<', now());
            })
            ->count();
    }

    /**
     * Check if installment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' || $this->remaining_amount <= 0;
    }
}
