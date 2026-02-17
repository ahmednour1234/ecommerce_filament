<?php

namespace App\Models\Housing;

use App\Models\HR\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HousingSalary extends Model
{
    protected $table = 'housing_salaries';

    protected $fillable = [
        'employee_id',
        'month',
        'basic_salary',
        'overtime_hours',
        'overtime_amount',
        'bonuses',
        'deductions',
        'net_salary',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'overtime_amount' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'overtime_hours' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($salary) {
            if (empty($salary->created_by) && auth()->check()) {
                $salary->created_by = auth()->id();
            }
            
            // Auto-calculate net salary
            $salary->net_salary = $salary->calculateNetSalary();
        });

        static::updating(function ($salary) {
            if (empty($salary->updated_by) && auth()->check()) {
                $salary->updated_by = auth()->id();
            }
            
            // Auto-calculate net salary
            $salary->net_salary = $salary->calculateNetSalary();
        });
    }

    public function calculateNetSalary(): float
    {
        $basic = (float) ($this->basic_salary ?? 0);
        $overtime = (float) ($this->overtime_amount ?? 0);
        $bonuses = (float) ($this->bonuses ?? 0);
        $deductions = (float) ($this->deductions ?? 0);

        return $basic + $overtime + $bonuses - $deductions;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForMonth($query, string $month)
    {
        return $query->where('month', $month);
    }

    public function scopeForEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
