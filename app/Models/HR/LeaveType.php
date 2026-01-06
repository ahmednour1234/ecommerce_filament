<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $table = 'hr_leave_types';

    protected $fillable = [
        'name_ar',
        'name_en',
        'allowed_days_per_year',
        'description_ar',
        'description_en',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allowed_days_per_year' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all leave requests for this type
     */
    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leave_type_id');
    }

    /**
     * Get all leave balances for this type
     */
    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'leave_type_id');
    }

    /**
     * Get name based on current locale
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    /**
     * Get description based on current locale
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->description_ar : $this->description_en;
    }

    /**
     * Scope to get only active leave types
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only inactive leave types
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}

