<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $fillable = [
        'shippable_type',
        'shippable_id',
        'shipping_provider_id',
        'tracking_number',
        'status',
        'currency_id',
        'price',
        'meta',
    ];

    protected $casts = [
        'price' => 'float',
        'meta'  => 'array',
    ];

    public function shippable()
    {
        return $this->morphTo();
    }

    public function provider()
    {
        return $this->belongsTo(ShippingProvider::class, 'shipping_provider_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
