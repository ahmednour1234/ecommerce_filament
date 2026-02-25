<?php

namespace App\Models\Housing;

use App\Models\Recruitment\Laborer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingSalaryItem extends Model
{
    use SoftDeletes;

    protected $table = 'housing_salary_items';

    protected $fillable = [
        'batch_id',
        'laborer_id',
        'basic_salary',
        'deductions_total',
        'net_salary',
        'status',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'deductions_total' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->net_salary = $item->basic_salary - $item->deductions_total;
        });
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(HousingSalaryBatch::class, 'batch_id');
    }

    public function laborer(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
