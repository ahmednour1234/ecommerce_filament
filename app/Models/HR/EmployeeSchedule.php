<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSchedule extends Model
{
    protected $table = 'hr_employee_schedules';

    protected $fillable = [
        'employee_id',
        'schedule_id',
        'date_from',
        'date_to',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the schedule template
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'schedule_id');
    }

    /**
     * Scope to get active schedules for a specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('date_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('date_to')
                    ->orWhere('date_to', '>=', $date);
            });
    }

    /**
     * Scope to get latest schedule for an employee
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('date_from', 'desc');
    }
}

