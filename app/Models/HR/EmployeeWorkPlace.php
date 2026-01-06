<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeWorkPlace extends Model
{
    protected $table = 'hr_employee_work_places';

    protected $fillable = [
        'employee_id',
        'work_place_id',
    ];

    /**
     * Get the employee
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the work place
     */
    public function workPlace(): BelongsTo
    {
        return $this->belongsTo(WorkPlace::class, 'work_place_id');
    }
}

