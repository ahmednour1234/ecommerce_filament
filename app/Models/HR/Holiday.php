<?php

namespace App\Models\HR;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    protected $table = 'hr_holidays';

    protected $fillable = [
        'name',
        'start_date',
        'days_count',
        'end_date',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days_count' => 'integer',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($holiday) {
            $holiday->computeEndDate();
        });
    }

    /**
     * Compute end_date based on start_date and days_count
     */
    public function computeEndDate(): void
    {
        if ($this->start_date && $this->days_count) {
            $this->end_date = Carbon::parse($this->start_date)
                ->addDays($this->days_count - 1);
        }
    }

    /**
     * Get the user who created this holiday
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this holiday overlaps with any existing holiday
     * 
     * @param int|null $excludeId Holiday ID to exclude from check (for updates)
     * @return bool
     */
    public function hasOverlap(?int $excludeId = null): bool
    {
        $query = static::where(function ($q) {
            $q->whereBetween('start_date', [$this->start_date, $this->end_date])
                ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                ->orWhere(function ($q2) {
                    $q2->where('start_date', '<=', $this->start_date)
                        ->where('end_date', '>=', $this->end_date);
                });
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}

