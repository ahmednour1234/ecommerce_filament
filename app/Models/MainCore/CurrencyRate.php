<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = [
        'base_currency_id',
        'target_currency_id',
        'rate',
        'valid_from',
        'source',
    ];

    protected $casts = [
        'rate'       => 'float',
        'valid_from' => 'datetime',
    ];

    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function targetCurrency()
    {
        return $this->belongsTo(Currency::class, 'target_currency_id');
    }
}
