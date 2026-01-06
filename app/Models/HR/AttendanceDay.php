<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceDay extends Model
{
    protected $table = 'hr_attendance_days';

    protected $fillable = [
        'employee_id',
        'date',
        'first_in',
        'last_out',
        'worked_minutes',
        'late_minutes',
        'overtime_minutes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'first_in' => 'datetime',
        'last_out' => 'datetime',
        'worked_minutes' => 'integer',
        'late_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Scope to get present days
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope to get absent days
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope to get days for a specific date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Get worked hours (decimal)
     */
    public function getWorkedHoursAttribute(): float
    {
        return round($this->worked_minutes / 60, 2);
    }
}

