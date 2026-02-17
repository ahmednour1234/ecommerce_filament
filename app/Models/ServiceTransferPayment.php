<?php

namespace App\Models;

use App\Models\MainCore\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class ServiceTransferPayment extends Model
{
    protected $fillable = [
        'transfer_id',
        'payment_no',
        'payment_date',
        'payment_method_id',
        'amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_no)) {
                $payment->payment_no = static::generatePaymentNo($payment->transfer_id);
            }
            
            if (empty($payment->created_by) && auth()->check()) {
                $payment->created_by = auth()->id();
            }
        });

        static::saved(function ($payment) {
            if ($payment->wasRecentlyCreated || $payment->wasChanged('amount')) {
                $transfer = $payment->transfer;
                if ($transfer) {
                    ServiceTransfer::recalculatePaymentStatus($transfer);
                }
            }
        });

        static::deleted(function ($payment) {
            $transfer = $payment->transfer;
            if ($transfer) {
                ServiceTransfer::recalculatePaymentStatus($transfer);
            }
        });
    }

    protected static function generatePaymentNo(int $transferId): int
    {
        return DB::transaction(function () use ($transferId) {
            $lastPayment = static::where('transfer_id', $transferId)
                ->orderBy('payment_no', 'desc')
                ->lockForUpdate()
                ->first();
            
            return $lastPayment ? $lastPayment->payment_no + 1 : 1;
        });
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(ServiceTransfer::class, 'transfer_id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
