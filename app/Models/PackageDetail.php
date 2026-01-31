<?php

namespace App\Models;

use App\Models\MainCore\Country;
use App\Models\Recruitment\Profession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class PackageDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'package_id',
        'code',
        'title',
        'country_id',
        'profession_id',
        'direct_cost',
        'gov_cost',
        'external_cost',
        'tax_percent',
        'tax_value',
        'total_with_tax',
    ];

    protected $casts = [
        'direct_cost' => 'decimal:2',
        'gov_cost' => 'decimal:2',
        'external_cost' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'tax_value' => 'decimal:2',
        'total_with_tax' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detail) {
            $subtotal = ($detail->direct_cost ?? 0) + 
                       ($detail->gov_cost ?? 0) + 
                       ($detail->external_cost ?? 0);
            
            $taxPercent = $detail->tax_percent ?? 0;
            $taxValue = $subtotal * ($taxPercent / 100);
            $totalWithTax = $subtotal + $taxValue;

            $detail->tax_value = $taxValue;
            $detail->total_with_tax = $totalWithTax;
        });

        static::created(function ($detail) {
            static::clearCache();
        });

        static::updated(function ($detail) {
            static::clearCache();
        });

        static::deleted(function ($detail) {
            static::clearCache();
        });

        static::restored(function ($detail) {
            static::clearCache();
        });
    }

    protected static function clearCache()
    {
        Cache::forget('packages.professions');
        Cache::forget('packages.countries');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }
}
