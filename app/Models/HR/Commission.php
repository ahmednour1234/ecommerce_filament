<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'commission_type_id',
        'value',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function commissionType(): BelongsTo
    {
        return $this->belongsTo(CommissionType::class, 'commission_type_id');
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(EmployeeCommissionTier::class, 'commission_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(EmployeeCommissionAssignment::class, 'commission_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
