<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $table = 'hr_attendance_logs';

    protected $fillable = [
        'employee_id',
        'log_datetime',
        'type',
        'source',
        'device_id',
        'raw_payload',
    ];

    protected $casts = [
        'log_datetime' => 'datetime',
        'source' => 'string',
        'raw_payload' => 'array',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the device (if from device)
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * Scope to get check-in logs
     */
    public function scopeCheckIn($query)
    {
        return $query->where('type', 'check_in');
    }

    /**
     * Scope to get check-out logs
     */
    public function scopeCheckOut($query)
    {
        return $query->where('type', 'check_out');
    }

    /**
     * Scope to get logs for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('log_datetime', $date);
    }

    /**
     * Scope to get logs from device
     */
    public function scopeFromDevice($query)
    {
        return $query->where('source', 'device');
    }
}

