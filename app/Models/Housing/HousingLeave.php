<?php

namespace App\Models\Housing;

use App\Models\HR\Employee;
use App\Models\HR\LeaveType;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HousingLeave extends Model
{
    protected $table = 'housing_leaves';

    protected $fillable = [
        'employee_id',
        'type',
        'leave_type_id',
        'start_date',
        'days',
        'end_date',
        'status',
        'reason',
        'notes',
        'approved_by',
        'approved_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'integer',
        'status' => 'string',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            if (empty($leave->created_by) && auth()->check()) {
                $leave->created_by = auth()->id();
            }
            
            // Auto-calculate end date if not set
            if ($leave->start_date && $leave->days && !$leave->end_date) {
                $leave->end_date = $leave->start_date->copy()->addDays($leave->days - 1);
            }
        });

        static::updating(function ($leave) {
            if (empty($leave->updated_by) && auth()->check()) {
                $leave->updated_by = auth()->id();
            }
            
            // Auto-calculate end date if days or start_date changed
            if ($leave->isDirty(['start_date', 'days']) && $leave->start_date && $leave->days) {
                $leave->end_date = $leave->start_date->copy()->addDays($leave->days - 1);
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeRecruitment($query)
    {
        return $query->where('type', 'recruitment');
    }

    public function scopeRental($query)
    {
        return $query->where('type', 'rental');
    }
}
