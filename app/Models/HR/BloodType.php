<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class BloodType extends Model
{
    protected $table = 'hr_blood_types';

    protected $fillable = [
        'name',
        'code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope to get only active blood types
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

