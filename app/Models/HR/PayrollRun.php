<?php

namespace App\Models\HR;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRun extends Model
{
    use SoftDeletes;

    protected $table = 'hr_payroll_runs';

    protected $fillable = [
        'year',
        'month',
        'department_id',
        'include_attendance_deductions',
        'include_loan_installments',
        'status',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'include_attendance_deductions' => 'boolean',
        'include_loan_installments' => 'boolean',
        'generated_at' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollRunItem::class, 'payroll_run_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function getPeriodAttribute(): string
    {
        return sprintf('%d-%02d', $this->year, $this->month);
    }

    public function getTotalNetSalaryAttribute(): float
    {
        return $this->items()->sum('net_salary');
    }

    public function getTotalEmployeesAttribute(): int
    {
        return $this->items()->count();
    }
}
