<?php

namespace Modules\CompanyVisas\Entities;

use App\Models\MainCore\Country;
use App\Models\Recruitment\Agent;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Profession;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyVisaContract extends Model
{
    protected $table = 'company_visa_contracts';

    protected $fillable = [
        'contract_no',
        'contract_date',
        'visa_request_id',
        'agent_id',
        'profession_id',
        'country_id',
        'workers_required',
        'linked_workers_count',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'workers_required' => 'integer',
        'linked_workers_count' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_no)) {
                $contract->contract_no = \Modules\CompanyVisas\Services\CompanyVisaContractService::generateContractNo();
            }
            if (empty($contract->created_by) && auth()->check()) {
                $contract->created_by = auth()->id();
            }
        });
    }

    public function visaRequest(): BelongsTo
    {
        return $this->belongsTo(CompanyVisaRequest::class, 'visa_request_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function workers(): BelongsToMany
    {
        return $this->belongsToMany(Laborer::class, 'company_visa_contract_workers', 'contract_id', 'worker_id')
            ->withPivot('cost_per_worker')
            ->withTimestamps()
            ->using(CompanyVisaContractWorker::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(CompanyVisaContractExpense::class, 'contract_id');
    }

    public function costs(): HasMany
    {
        return $this->hasMany(CompanyVisaContractCost::class, 'contract_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CompanyVisaContractDocument::class, 'contract_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active', 'completed' => 'success',
            'cancelled' => 'danger',
            'draft' => 'gray',
            default => 'gray',
        };
    }

    public function updateLinkedWorkersCount(): void
    {
        $this->linked_workers_count = $this->workers()->count();
        $this->save();
    }

    public function incrementLinkedWorkers(int $count = 1): void
    {
        $this->increment('linked_workers_count', $count);
        if ($this->visa_request_id) {
            $this->visaRequest->incrementUsedCount($count);
        }
    }

    public function decrementLinkedWorkers(int $count = 1): void
    {
        $this->decrement('linked_workers_count', $count);
        if ($this->visa_request_id) {
            $this->visaRequest->decrementUsedCount($count);
        }
    }
}
