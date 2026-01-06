<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'hr_banks';

    protected $fillable = [
        'name',
        'iban_prefix',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Scope to get only active banks
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

