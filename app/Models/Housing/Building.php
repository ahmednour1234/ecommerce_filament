<?php

namespace App\Models\Housing;

use App\Models\MainCore\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    use SoftDeletes;

    protected $table = 'housing_buildings';

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'branch_id',
        'address',
        'capacity',
        'available_capacity',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'available_capacity' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(HousingAssignment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_capacity', '>', 0)
            ->where('status', 'active');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
