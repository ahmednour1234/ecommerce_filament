<?php

namespace App\Models\Recruitment;

use App\Models\Client;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\User;
use App\Services\Recruitment\RecruitmentContractService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecruitmentContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_no',
        'client_id',
        'branch_id',
        'gregorian_request_date',
        'hijri_request_date',
        'visa_type',
        'visa_no',
        'visa_date',
        'arrival_country_id',
        'departure_country_id',
        'receiving_station_id',
        'profession_id',
        'gender',
        'experience',
        'religion',
        'workplace_ar',
        'workplace_en',
        'monthly_salary',
        'musaned_contract_no',
        'musaned_documentation_contract_no',
        'musaned_auth_no',
        'musaned_contract_date',
        'direct_cost',
        'internal_ticket_cost',
        'external_cost',
        'vat_cost',
        'gov_cost',
        'total_cost',
        'paid_total',
        'remaining_total',
        'payment_status',
        'status',
        'notes',
        'visa_image',
        'musaned_contract_file',
        'worker_id',
        'created_by',
    ];

    protected $casts = [
        'gregorian_request_date' => 'date',
        'visa_date' => 'date',
        'musaned_contract_date' => 'date',
        'direct_cost' => 'decimal:2',
        'internal_ticket_cost' => 'decimal:2',
        'external_cost' => 'decimal:2',
        'vat_cost' => 'decimal:2',
        'gov_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'paid_total' => 'decimal:2',
        'remaining_total' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_no)) {
                $service = app(RecruitmentContractService::class);
                $contract->contract_no = $service->generateContractNo();
            }
            
            if (empty($contract->created_by) && auth()->check()) {
                $contract->created_by = auth()->id();
            }
        });

        static::saving(function ($contract) {
            if ($contract->isDirty(['direct_cost', 'internal_ticket_cost', 'external_cost', 'vat_cost', 'gov_cost']) || $contract->isNew()) {
                $service = app(RecruitmentContractService::class);
                $totals = $service->computeTotals($contract);
                $contract->fill($totals);
            }
        });

        static::updating(function ($contract) {
            if ($contract->isDirty('status')) {
                $oldStatus = $contract->getOriginal('status');
                $newStatus = $contract->status;
                
                if ($oldStatus !== $newStatus) {
                    $service = app(RecruitmentContractService::class);
                    $service->logStatusChange($contract, $oldStatus, $newStatus);
                }
            }
        });

        static::created(function ($contract) {
            $service = app(RecruitmentContractService::class);
            $service->logStatusChange($contract, null, $contract->status, 'Contract created');
            static::clearCache();
        });

        static::updated(function ($contract) {
            static::clearCache();
        });

        static::deleted(function ($contract) {
            static::clearCache();
        });

        static::restored(function ($contract) {
            static::clearCache();
        });
    }

    protected static function clearCache()
    {
        \Illuminate\Support\Facades\Cache::forget('recruitment_contracts.branches');
        \Illuminate\Support\Facades\Cache::forget('recruitment_contracts.clients');
        \Illuminate\Support\Facades\Cache::forget('recruitment_contracts.workers');
        \Illuminate\Support\Facades\Cache::forget('recruitment_contracts.countries');
        \Illuminate\Support\Facades\Cache::forget('recruitment_contracts.professions');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }


    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function financeLinks(): HasMany
    {
        return $this->hasMany(RecruitmentContractFinanceLink::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(RecruitmentContractFinanceLink::class)->where('type', 'receipt');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(RecruitmentContractFinanceLink::class)->where('type', 'expense');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(RecruitmentContractStatusLog::class);
    }

    public function complaints(): MorphMany
    {
        return $this->morphMany(\App\Models\Complaint::class, 'contract');
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'worker_received');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeReturned($query)
    {
        return $query->whereIn('status', ['returned', 'cancelled']);
    }

    public function scopeInWarranty($query)
    {
        $warrantyDays = defined('self::WARRANTY_DAYS') ? self::WARRANTY_DAYS : 30;
        return $query->where('created_at', '>=', now()->subDays($warrantyDays))
            ->where('created_at', '<=', now());
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'contract_signed');
    }

    public function scopeVisaIssued($query)
    {
        return $query->where('status', 'visa_issued');
    }

    public function scopeArrivalTicketIssued($query)
    {
        return $query->where('status', 'ticket_booked');
    }
}
