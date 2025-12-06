<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    protected $fillable = [
        'name',
        'code',
        'driver',
        'config',
        'is_active',
    ];

    protected $casts = [
        'config'    => 'array',
        'is_active' => 'boolean',
    ];

    public function methods()
    {
        return $this->hasMany(PaymentMethod::class, 'provider_id');
    }
}
