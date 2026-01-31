<?php

namespace App\Models\Rental;

use App\Models\Finance\BranchTransaction;
use App\Models\User;
use App\Services\Rental\RentalContractService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalContractPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'rental_contract_id',
        'finance_transaction_id',
        'amount',
        'paid_at',
        'method_id',
        'method',
        'reference',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($payment) {
            if (empty($payment->paid_at)) {
                $payment->paid_at = now();
            }
            
            if (empty($payment->created_by) && auth()->check()) {
                $payment->created_by = auth()->id();
            }
        });

        static::saved(function ($payment) {
            if ($payment->wasRecentlyCreated || $payment->wasChanged('amount') || $payment->wasChanged('status')) {
                $contract = $payment->contract;
                if ($contract) {
                    $service = app(RentalContractService::class);
                    $totals = $service->computeTotals($contract);
                    $contract->update($totals);
                }
            }
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });

        static::restored(function () {
            static::clearCache();
        });
    }

    protected static function clearCache()
    {
        \Illuminate\Support\Facades\Cache::forget('rental.branches');
        \Illuminate\Support\Facades\Cache::forget('rental.customers');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(RentalContract::class, 'rental_contract_id');
    }

    public function financeTransaction(): BelongsTo
    {
        return $this->belongsTo(BranchTransaction::class, 'finance_transaction_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }
}
