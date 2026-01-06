<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $table = 'hr_devices';

    protected $fillable = [
        'name',
        'type',
        'ip',
        'serial_number',
        'api_key',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get all attendance logs from this device
     */
    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'device_id');
    }

    /**
     * Scope to get only active devices
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to find device by API key
     */
    public function scopeByApiKey($query, $apiKey)
    {
        return $query->where('api_key', $apiKey);
    }
}

