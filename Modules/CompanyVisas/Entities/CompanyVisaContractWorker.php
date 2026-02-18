<?php

namespace Modules\CompanyVisas\Entities;

use App\Models\Recruitment\Laborer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CompanyVisaContractWorker extends Pivot
{
    protected $table = 'company_visa_contract_workers';

    protected $fillable = [
        'contract_id',
        'worker_id',
        'cost_per_worker',
    ];

    protected $casts = [
        'cost_per_worker' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($pivot) {
            $contract = CompanyVisaContract::find($pivot->contract_id);
            if ($contract) {
                $contract->incrementLinkedWorkers(1);
            }
        });

        static::deleted(function ($pivot) {
            $contract = CompanyVisaContract::find($pivot->contract_id);
            if ($contract) {
                $contract->decrementLinkedWorkers(1);
            }
        });
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(CompanyVisaContract::class, 'contract_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Laborer::class, 'worker_id');
    }
}
