<?php

namespace App\Models\Housing;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingSalaryBatch extends Model
{
    use SoftDeletes;

    protected $table = 'housing_salary_batches';

    protected $fillable = [
        'month',
        'total_salaries',
        'total_paid',
        'total_pending',
        'total_deductions',
        'created_by',
    ];

    protected $casts = [
        'total_salaries' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'total_pending' => 'decimal:2',
        'total_deductions' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($batch) {
            if (empty($batch->created_by) && auth()->check()) {
                $batch->created_by = auth()->id();
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(HousingSalaryItem::class, 'batch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForMonth($query, string $month)
    {
        return $query->where('month', $month);
    }
}
