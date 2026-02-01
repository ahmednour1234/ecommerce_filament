<?php

namespace App\Models\Biometric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BiometricDevice extends Model
{
    protected $table = 'biometric_devices';

    protected $fillable = [
        'name',
        'serial_number',
        'ip_address',
        'api_key',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(BiometricAttendance::class, 'device_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByApiKey($query, $apiKey)
    {
        return $query->where('api_key', $apiKey);
    }
}
