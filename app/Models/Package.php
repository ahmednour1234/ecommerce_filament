<?php

namespace App\Models;

use App\Models\MainCore\Country;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'description',
        'country_id',
        'status',
        'duration_type',
        'duration',
        'base_price',
        'external_costs',
        'worker_salary',
        'gov_fees',
        'tax_percent',
        'tax_value',
        'total',
        'created_by',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'external_costs' => 'decimal:2',
        'worker_salary' => 'decimal:2',
        'gov_fees' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_value' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($package) {
            $subtotal = ($package->base_price ?? 0) + 
                       ($package->external_costs ?? 0) + 
                       ($package->worker_salary ?? 0) + 
                       ($package->gov_fees ?? 0);
            
            $taxPercent = $package->tax_percent ?? 0;
            $taxValue = $subtotal * ($taxPercent / 100);
            $total = $subtotal + $taxValue;

            $package->tax_value = $taxValue;
            $package->total = $total;

            if (empty($package->created_by) && auth()->check()) {
                $package->created_by = auth()->id();
            }
        });

        static::created(function ($package) {
            static::clearCache($package);
        });

        static::updated(function ($package) {
            static::clearCache($package);
        });

        static::deleted(function ($package) {
            static::clearCache($package);
        });

        static::restored(function ($package) {
            static::clearCache($package);
        });
    }

    protected static function clearCache($package)
    {
        Cache::forget("packages.type.{$package->type}");
        Cache::forget('packages.countries');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function packageDetails(): HasMany
    {
        return $this->hasMany(PackageDetail::class);
    }
}
