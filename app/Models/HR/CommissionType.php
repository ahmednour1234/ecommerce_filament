<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'commission_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
