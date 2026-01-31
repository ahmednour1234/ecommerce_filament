<?php

namespace App\Models\Rental;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalCancelRefundRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'rental_contract_id',
        'type',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'refund_amount',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(RentalContract::class, 'rental_contract_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancel($query)
    {
        return $query->where('type', 'cancel');
    }

    public function scopeRefund($query)
    {
        return $query->where('type', 'refund');
    }
}
