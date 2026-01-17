<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRunItem extends Model
{
    use SoftDeletes;

    protected $table = 'hr_payroll_run_items';

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'basic_salary',
        'total_earnings',
        'total_deductions',
        'net_salary',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function payrollRun(): BelongsTo
    {
        return $this->belongsTo(PayrollRun::class, 'payroll_run_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PayrollRunItemLine::class, 'payroll_run_item_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function getEarningsLinesAttribute()
    {
        return $this->lines()->where('type', 'earning')->get();
    }

    public function getDeductionsLinesAttribute()
    {
        return $this->lines()->where('type', 'deduction')->get();
    }
}
