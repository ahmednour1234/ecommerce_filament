<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    protected $table = 'hr_work_schedules';

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'break_minutes',
        'late_grace_minutes',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'break_minutes' => 'integer',
        'late_grace_minutes' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get all employee schedules using this template
     */
    public function employeeSchedules(): HasMany
    {
        return $this->hasMany(EmployeeSchedule::class, 'schedule_id');
    }

    /**
     * Scope to get only active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Calculate total working minutes (excluding breaks)
     */
    public function getTotalWorkingMinutesAttribute(): int
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        $totalMinutes = $start->diffInMinutes($end);
        return max(0, $totalMinutes - $this->break_minutes);
    }
}

