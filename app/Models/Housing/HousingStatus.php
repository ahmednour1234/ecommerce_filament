<?php

namespace App\Models\Housing;

use Illuminate\Database\Eloquent\Model;

class HousingStatus extends Model
{
    protected $table = 'housing_statuses';

    protected $fillable = [
        'key',
        'name_ar',
        'name_en',
        'color',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
