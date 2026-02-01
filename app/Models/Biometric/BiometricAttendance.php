<?php

namespace App\Models\Biometric;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricAttendance extends Model
{
    protected $table = 'biometric_attendances';

    protected $fillable = [
        'device_id',
        'user_id',
        'attended_at',
        'state',
        'type',
        'ip_address',
        'raw_data',
        'processed',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
        'raw_data' => 'array',
        'processed' => 'boolean',
        'state' => 'integer',
        'type' => 'integer',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(BiometricDevice::class, 'device_id');
    }

    public function scopeUnprocessed($query)
    {
        return $query->where('processed', false);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attended_at', $date);
    }
}
