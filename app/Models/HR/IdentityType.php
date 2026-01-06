<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class IdentityType extends Model
{
    protected $table = 'hr_identity_types';

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope to get only active identity types
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

