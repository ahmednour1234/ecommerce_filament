<?php

namespace App\Models\Sales;

use App\Models\MainCore\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Installment extends Model
{
    protected $fillable = [
        'installmentable_type',
        'installmentable_id',
        'installment_number',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'payment_method_id',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date',
    ];

    /**
     * Get the parent model (Order or Invoice)
     */
    public function installmentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the payment method
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(\App\Models\MainCore\PaymentMethod::class);
    }

    /**
     * Check if installment is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid' || !is_null($this->paid_date);
    }

    /**
     * Check if installment is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date && $this->due_date->isPast();
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        if ($this->isPaid()) {
            return 0;
        }
        return $this->amount;
    }
}
