<?php

namespace App\Models\Accounting;

use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Traits\HasBranch;
use App\Traits\HasCostCenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasBranch, HasCostCenter;

    protected $fillable = [
        'code',
        'name',
        'description',
        'account_id',
        'branch_id',
        'cost_center_id',
        'type',
        'category',
        'purchase_cost',
        'current_value',
        'purchase_date',
        'useful_life_years',
        'depreciation_rate',
        'location',
        'serial_number',
        'status',
        'metadata',
    ];

    protected $casts = [
        'purchase_cost' => 'decimal:2',
        'current_value' => 'decimal:2',
        'purchase_date' => 'date',
        'depreciation_rate' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the account
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the cost center
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Calculate accumulated depreciation
     */
    public function getAccumulatedDepreciationAttribute(): float
    {
        if (!$this->depreciation_rate || !$this->useful_life_years || !$this->purchase_date) {
            return 0;
        }

        $yearsSincePurchase = $this->purchase_date->diffInYears(now());
        $annualDepreciation = $this->purchase_cost * ($this->depreciation_rate / 100);
        return min($annualDepreciation * $yearsSincePurchase, $this->purchase_cost);
    }

    /**
     * Calculate book value
     */
    public function getBookValueAttribute(): float
    {
        return $this->purchase_cost - $this->accumulated_depreciation;
    }


    /**
     * Scope to get only active assets
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
