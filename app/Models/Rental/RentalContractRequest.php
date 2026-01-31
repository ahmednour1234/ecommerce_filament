<?php

namespace App\Models\Rental;

use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\Recruitment\Profession;
use App\Models\Sales\Customer;
use App\Services\Rental\RentalContractService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalContractRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'request_no',
        'branch_id',
        'customer_id',
        'desired_package_id',
        'desired_country_id',
        'profession_id',
        'worker_gender',
        'start_date',
        'duration_type',
        'duration',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($request) {
            if (empty($request->request_no)) {
                $service = app(RentalContractService::class);
                $request->request_no = $service->generateRequestNo();
            }
        });

        static::created(function () {
            static::clearCache();
        });

        static::updated(function () {
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

    public function desiredPackage(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Package::class, 'desired_package_id');
    }

    public function desiredCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'desired_country_id');
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeConverted($query)
    {
        return $query->where('status', 'converted');
    }
}
