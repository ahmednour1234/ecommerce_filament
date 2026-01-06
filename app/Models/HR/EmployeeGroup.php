<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeGroup extends Model
{
    protected $table = 'hr_employee_groups';

    protected $fillable = [
        'name',
        'status',
        'default_schedule_id',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the default schedule for this group
     */
    public function defaultSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'default_schedule_id');
    }

    /**
     * Get all members of this group
     */
    public function members(): HasMany
    {
        return $this->hasMany(EmployeeGroupMember::class, 'group_id');
    }

    /**
     * Get all employees in this group
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'hr_employee_group_members', 'group_id', 'employee_id');
    }

    /**
     * Scope to get only active groups
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}

