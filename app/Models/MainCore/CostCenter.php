<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostCenter extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the parent cost center
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'parent_id');
    }

    /**
     * Get child cost centers
     */
    public function children(): HasMany
    {
        return $this->hasMany(CostCenter::class, 'parent_id');
    }

    /**
     * Scope to get only active cost centers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

