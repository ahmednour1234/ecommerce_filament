<?php

namespace App\Traits;

use App\Models\MainCore\CostCenter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCostCenter
{
    /**
     * Get the cost center that owns this model
     */
    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    /**
     * Scope a query to only include records for a specific cost center
     */
    public function scopeForCostCenter($query, $costCenterId)
    {
        return $query->where('cost_center_id', $costCenterId);
    }
}

