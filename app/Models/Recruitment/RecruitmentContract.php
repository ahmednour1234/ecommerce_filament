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

    public const SECTION_CUSTOMER_SERVICE = 'customer_service';
    public const SECTION_ACCOUNTS = 'accounts';
    public const SECTION_COORDINATION = 'coordination';

    public static function currentSectionOptions(): array
    {
        return [
            self::SECTION_CUSTOMER_SERVICE => 'خدمة العملاء',
            self::SECTION_ACCOUNTS => 'قسم الحسابات',
            self::SECTION_COORDINATION => 'قسم التنسيق',
        ];
    }

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
        'nationality_id',
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
        'payment_status_code',
        'is_paid',
        'paid_at',
        'status',
        'current_section',
        'arrival_date',
        'trial_end_date',
        'contract_end_date',
        'notes',
        'client_text_message',
        'client_rating',
        'client_rating_proof_image',
        'visa_image',
        'musaned_contract_file',
        'worker_id',
        'created_by',
        'marketer_id',
    ];

    protected $casts = [
        'gregorian_request_date' => 'date',
        'visa_date' => 'date',
        'musaned_contract_date' => 'date',
        'arrival_date' => 'date',
        'trial_end_date' => 'date',
        'contract_end_date' => 'date',
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
            $service = app(RecruitmentContractService::class);
            $totals = $service->computeTotals($contract);
            $contract->fill($totals);

            $arrivalDate = null;

            if ($contract->visa_date) {
                $arrivalDate = \Carbon\Carbon::parse($contract->visa_date);
            } elseif ($contract->status === 'received') {
                if ($contract->exists) {
                    $receivedLog = $contract->statusLogs()
                        ->where('new_status', 'received')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($receivedLog) {
                        $arrivalDate = $receivedLog->status_date 
                            ? \Carbon\Carbon::parse($receivedLog->status_date)
                            : \Carbon\Carbon::parse($receivedLog->created_at);
                    }
                }
            }

            if ($arrivalDate) {
                $shouldUpdate = !$contract->arrival_date 
                    || $contract->isDirty('visa_date') 
                    || ($contract->isDirty('status') && $contract->status === 'received');
                
                if ($shouldUpdate) {
                    $contract->arrival_date = $arrivalDate->toDateString();
                    $contract->trial_end_date = $arrivalDate->copy()->addDays(90)->toDateString();
                    $contract->contract_end_date = $arrivalDate->copy()->addYears(2)->toDateString();
                }
            }
        });

        static::created(function ($contract) {
            $service = app(RecruitmentContractService::class);
            $service->logStatusChange($contract, null, $contract->status ?? 'new', 'Contract created');
            static::clearCache();
        });

        static::updated(function ($contract) {
            static::clearCache();
            
            if ($contract->wasChanged('status') && $contract->status === 'received') {
                $arrivalDate = null;
                
                if ($contract->visa_date) {
                    $arrivalDate = \Carbon\Carbon::parse($contract->visa_date);
                } else {
                    $receivedLog = $contract->statusLogs()
                        ->where('new_status', 'received')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($receivedLog) {
                        $arrivalDate = $receivedLog->status_date 
                            ? \Carbon\Carbon::parse($receivedLog->status_date)
                            : \Carbon\Carbon::parse($receivedLog->created_at);
                    }
                }
                
                if ($arrivalDate && (!$contract->arrival_date || $contract->arrival_date !== $arrivalDate->toDateString())) {
                    $contract->updateQuietly([
                        'arrival_date' => $arrivalDate->toDateString(),
                        'trial_end_date' => $arrivalDate->copy()->addDays(90)->toDateString(),
                        'contract_end_date' => $arrivalDate->copy()->addYears(2)->toDateString(),
                    ]);
                }
            }
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

    public function nationality(): BelongsTo
    {
        return $this->belongsTo(Nationality::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function marketer(): BelongsTo
    {
        return $this->belongsTo(\App\Models\HR\Employee::class, 'marketer_id');
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
        return $query->where('status', 'received');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'return_during_warranty');
    }

    public function scopeInWarranty($query)
    {
        $warrantyDays = defined('self::WARRANTY_DAYS') ? self::WARRANTY_DAYS : 30;
        return $query->where('created_at', '>=', now()->subDays($warrantyDays))
            ->where('created_at', '<=', now());
    }

    public function scopeVisaIssued($query)
    {
        return $query->where('status', 'visa_issued');
    }

    public function scopeWaitingFlightBooking($query)
    {
        return $query->where('status', 'waiting_flight_booking');
    }
}
