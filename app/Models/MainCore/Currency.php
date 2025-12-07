<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'precision',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public function baseRates()
    {
        return $this->hasMany(CurrencyRate::class, 'base_currency_id');
    }

    public function targetRates()
    {
        return $this->hasMany(CurrencyRate::class, 'target_currency_id');
    }

    /**
     * Scope to get only active currencies
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
