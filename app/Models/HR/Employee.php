<?php

namespace App\Models\HR;

use App\Models\MainCore\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected $table = 'hr_employees';

    protected $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'fingerprint_device_id',
        'profile_image',
        'hire_date',
        'branch_id',
        'department_id',
        'position_id',
        'location_id',
        'basic_salary',
        'cv_file',
        'address',
        'city',
        'country',
        'identity_type_id',
        'identity_number',
        'identity_expiry_date',
        'blood_type_id',
        'emergency_contact_name',
        'emergency_contact_phone',
        'bank_id',
        'bank_name_text',
        'bank_account_number',
        'iban',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'identity_expiry_date' => 'date',
        'basic_salary' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Get the branch this employee belongs to
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the department this employee belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the position this employee holds
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Get the location this employee works at
     * Note: Location model assumed to exist, adjust namespace if needed
     */
    public function location(): BelongsTo
    {
        // Assuming Location model exists in MainCore namespace
        // If not, this can be adjusted or removed
        if (class_exists(\App\Models\MainCore\Location::class)) {
            return $this->belongsTo(\App\Models\MainCore\Location::class, 'location_id');
        }
        // Fallback: use Branch if Location doesn't exist
        return $this->belongsTo(Branch::class, 'location_id');
    }

    /**
     * Get the identity type
     */
    public function identityType(): BelongsTo
    {
        return $this->belongsTo(IdentityType::class, 'identity_type_id');
    }

    /**
     * Get the blood type
     */
    public function bloodType(): BelongsTo
    {
        return $this->belongsTo(BloodType::class, 'blood_type_id');
    }

    /**
     * Get the bank
     */
    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * Scope to get only active employees
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive employees
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Get the work place assignment
     */
    public function workPlace()
    {
        return $this->hasOne(EmployeeWorkPlace::class, 'employee_id');
    }

    /**
     * Get all group memberships
     */
    public function groupMemberships()
    {
        return $this->hasMany(EmployeeGroupMember::class, 'employee_id');
    }

    /**
     * Get all groups this employee belongs to
     */
    public function groups()
    {
        return $this->belongsToMany(EmployeeGroup::class, 'hr_employee_group_members', 'employee_id', 'group_id');
    }

    /**
     * Get all schedules assigned to this employee
     */
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_id');
    }

    /**
     * Get all excuse requests
     */
    public function excuseRequests()
    {
        return $this->hasMany(ExcuseRequest::class, 'employee_id');
    }

    /**
     * Get all attendance logs
     */
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'employee_id');
    }

    /**
     * Get all attendance days
     */
    public function attendanceDays()
    {
        return $this->hasMany(AttendanceDay::class, 'employee_id');
    }

    /**
     * Get all leave requests
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'employee_id');
    }

    /**
     * Get all leave balances
     */
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id');
    }
}

