<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    protected $table = 'hr_leave_balances';

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'quota',
        'used',
        'remaining',
    ];

    protected $casts = [
        'year' => 'integer',
        'quota' => 'integer',
        'used' => 'integer',
        'remaining' => 'integer',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the leave type
     */
    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    /**
     * Scope to get balances for a specific employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to get balances for a specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope to get balances for a specific leave type
     */
    public function scopeForLeaveType($query, $leaveTypeId)
    {
        return $query->where('leave_type_id', $leaveTypeId);
    }

    /**
     * Update remaining balance based on used days
     */
    public function updateRemaining(): void
    {
        $this->remaining = max(0, $this->quota - $this->used);
        $this->save();
    }
}

