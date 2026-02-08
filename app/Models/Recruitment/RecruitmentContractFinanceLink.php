<?php

namespace App\Models\Recruitment;

use App\Models\Finance\BranchTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentContractFinanceLink extends Model
{
    protected $fillable = [
        'recruitment_contract_id',
        'finance_transaction_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($link) {
            $contract = $link->contract;
            if ($contract) {
                $service = app(\App\Services\Recruitment\RecruitmentContractService::class);
                $totals = $service->computeTotals($contract);
                $contract->update($totals);
            }
        });

        static::deleted(function ($link) {
            $contract = $link->contract;
            if ($contract) {
                $service = app(\App\Services\Recruitment\RecruitmentContractService::class);
                $totals = $service->computeTotals($contract);
                $contract->update($totals);
            }
        });
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(RecruitmentContract::class);
    }

    public function financeTransaction(): BelongsTo
    {
        return $this->belongsTo(BranchTransaction::class, 'finance_transaction_id');
    }
}
