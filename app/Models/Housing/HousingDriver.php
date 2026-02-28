<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingDriver extends Model
{
    use SoftDeletes;

    protected $table = 'housing_drivers';

    protected $fillable = [
        'name',
        'phone',
        'identity_number',
        'license_number',
        'license_expiry',
    ];

    protected $casts = [
        'license_expiry' => 'date',
    ];

    public function scopeLicenseExpiring($query, int $days = 30)
    {
        return $query->where('license_expiry', '<=', now()->addDays($days))
            ->where('license_expiry', '>=', now());
    }

    public function scopeLicenseExpired($query)
    {
        return $query->where('license_expiry', '<', now());
    }

    public function cars(): HasMany
    {
        return $this->hasMany(HousingCar::class);
    }
}
