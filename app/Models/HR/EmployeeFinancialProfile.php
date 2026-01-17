<?php

namespace App\Models\HR;

use App\Models\MainCore\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeFinancialProfile extends Model
{
    use SoftDeletes;

    protected $table = 'hr_employee_financial_profiles';

    protected $fillable = [
        'employee_id',
        'base_salary',
        'currency_id',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'joined_at' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function salaryItems(): HasMany
    {
        return $this->hasMany(EmployeeSalaryItem::class, 'profile_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTotalEarningsAttribute(): float
    {
        return (float) $this->salaryItems()
            ->join('hr_salary_components', 'hr_employee_salary_items.component_id', '=', 'hr_salary_components.id')
            ->where('hr_salary_components.type', 'earning')
            ->sum('hr_employee_salary_items.amount');
    }

    public function getTotalDeductionsAttribute(): float
    {
        return (float) $this->salaryItems()
            ->join('hr_salary_components', 'hr_employee_salary_items.component_id', '=', 'hr_salary_components.id')
            ->where('hr_salary_components.type', 'deduction')
            ->sum('hr_employee_salary_items.amount');
    }
}
