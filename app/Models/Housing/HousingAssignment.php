<?php

namespace App\Models\Housing;

use App\Models\Recruitment\Laborer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HousingAssignment extends Model
{
    use SoftDeletes;

    protected $table = 'housing_assignments';

    protected $fillable = [
        'laborer_id',
        'building_id',
        'unit_id',
        'start_date',
        'end_date',
        'rent_amount',
        'status_id',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'rent_amount' => 'decimal:2',
    ];

    public function laborer(): BelongsTo
    {
        return $this->belongsTo(Laborer::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(HousingStatus::class, 'status_id');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('end_date');
    }

    public function scopeForLaborer($query, int $laborerId)
    {
        return $query->where('laborer_id', $laborerId);
    }
}
