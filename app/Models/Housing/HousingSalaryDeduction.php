<?php

namespace App\Models\Housing;

use App\Models\Recruitment\Laborer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingSalaryDeduction extends Model
{
    use SoftDeletes;

    protected $table = 'housing_salary_deductions';

    protected $fillable = [
        'laborer_id',
        'deduction_date',
        'deduction_type',
        'amount',
        'reason',
        'notes',
        'status',
    ];

    protected $casts = [
        'deduction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function laborer(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }
}
