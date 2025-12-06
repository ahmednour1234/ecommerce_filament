<?php

namespace App\Models\MainCore;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'payable_type',
        'payable_id',
        'user_id',
        'payment_method_id',
        'provider_id',
        'currency_id',
        'amount',
        'status',
        'provider_reference',
        'meta',
        'paid_at',
    ];

    protected $casts = [
        'amount'   => 'float',
        'meta'     => 'array',
        'paid_at'  => 'datetime',
    ];

    public function payable()
    {
        return $this->morphTo();
    }

    public function method()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function provider()
    {
        return $this->belongsTo(PaymentProvider::class, 'provider_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
