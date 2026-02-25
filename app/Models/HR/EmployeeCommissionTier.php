<?php

namespace App\Models\HR;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeCommissionTier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'commission_id',
        'contracts_from',
        'contracts_to',
        'amount_per_contract',
        'is_active',
    ];

    protected $casts = [
        'contracts_from' => 'integer',
        'contracts_to' => 'integer',
        'amount_per_contract' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class, 'commission_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function matchesContractCount(int $count): bool
    {
        return $count >= $this->contracts_from && $count <= $this->contracts_to;
    }
}
