<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'provider_id',
        'name',
        'code',
        'fee_fixed',
        'fee_percent',
        'is_default',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'fee_fixed'   => 'float',
        'fee_percent' => 'float',
        'is_default'  => 'boolean',
        'is_active'   => 'boolean',
    ];

    public function provider()
    {
        return $this->belongsTo(PaymentProvider::class, 'provider_id');
    }
}
