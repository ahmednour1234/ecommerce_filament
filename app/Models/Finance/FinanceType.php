<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinanceType extends Model
{
    protected $fillable = [
        'kind',
        'name',
        'code',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'name' => 'array',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    public function getNameTextAttribute(): string
    {
        $locale = app()->getLocale();
        return $this->name[$locale] ?? $this->name['ar'] ?? '';
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BranchTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIncome($query)
    {
        return $query->where('kind', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('kind', 'expense');
    }
}
