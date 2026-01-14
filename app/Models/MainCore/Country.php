<?php

namespace App\Models\MainCore;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'iso2',
        'iso3',
        'name',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
    ];

    public function getNameTextAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['en'] ?? '';
    }
}
