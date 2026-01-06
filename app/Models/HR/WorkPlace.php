<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkPlace extends Model
{
    protected $table = 'hr_work_places';

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meters',
        'status',
        'default_schedule_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'radius_meters' => 'integer',
        'status' => 'boolean',
    ];

    /**
     * Get the default schedule for this work place
     */
    public function defaultSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'default_schedule_id');
    }

    /**
     * Get all employees assigned to this work place
     */
    public function employees(): HasMany
    {
        return $this->hasMany(EmployeeWorkPlace::class, 'work_place_id');
    }

    /**
     * Scope to get only active work places
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}

