<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanType extends Model
{
    protected $table = 'hr_loan_types';

    protected $fillable = [
        'name_json',
        'description_json',
        'max_amount',
        'max_installments',
        'is_active',
    ];

    protected $casts = [
        'name_json' => 'array',
        'description_json' => 'array',
        'max_amount' => 'decimal:2',
        'max_installments' => 'integer',
        'is_active' => 'boolean',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'loan_type_id');
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $name = $this->name_json ?? [];
        return $name[$locale] ?? $name['en'] ?? '';
    }

    public function getDescriptionAttribute(): ?string
    {
        if (!$this->description_json) {
            return null;
        }
        $locale = app()->getLocale();
        $description = $this->description_json;
        return $description[$locale] ?? $description['en'] ?? null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
