<?php

namespace App\Models\Rental;

use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Profession;
use App\Models\Sales\Customer;
use App\Models\User;
use App\Services\Rental\RentalContractService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalContract extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contract_no',
        'request_no',
        'branch_id',
        'customer_id',
        'worker_id',
        'country_id',
        'profession_id',
        'package_id',
        'status',
        'payment_status',
        'start_date',
        'end_date',
        'duration_type',
        'duration',
        'tax_percent',
        'discount_type',
        'discount_value',
        'subtotal',
        'tax_value',
        'total',
        'paid_total',
        'remaining_total',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'tax_percent' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_value' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_total' => 'decimal:2',
        'remaining_total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            if (empty($contract->contract_no)) {
                $service = app(RentalContractService::class);
                $contract->contract_no = $service->generateContractNo();
            }
            
            if (empty($contract->created_by) && auth()->check()) {
                $contract->created_by = auth()->id();
            }
        });

        static::saving(function ($contract) {
            if ($contract->isDirty(['package_id', 'discount_type', 'discount_value', 'tax_percent']) || $contract->isNew()) {
                $service = app(RentalContractService::class);
                $totals = $service->computeTotals($contract);
                $contract->fill($totals);
            }
        });

        static::updating(function ($contract) {
            if ($contract->isDirty('status')) {
                $oldStatus = $contract->getOriginal('status');
                $newStatus = $contract->status;
                
                if ($oldStatus !== $newStatus) {
                    $service = app(RentalContractService::class);
                    $service->logStatusChange($contract, $oldStatus, $newStatus);
                }
            }
        });

        static::created(function ($contract) {
            $service = app(RentalContractService::class);
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
        \Illuminate\Support\Facades\Cache::forget('rental.branches');
        \Illuminate\Support\Facades\Cache::forget('rental.customers');
        \Illuminate\Support\Facades\Cache::forget('rental.workers');
        \Illuminate\Support\Facades\Cache::forget('rental.packages');
        \Illuminate\Support\Facades\Cache::forget('rental.countries');
        \Illuminate\Support\Facades\Cache::forget('rental.professions');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Laborer::class, 'worker_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Package::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(RentalContractPayment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(RentalContractStatusLog::class);
    }

    public function cancelRefundRequests(): HasMany
    {
        return $this->hasMany(RentalCancelRefundRequest::class);
    }

    public function complaints(): MorphMany
    {
        return $this->morphMany(\App\Models\Complaint::class, 'contract');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
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
}
