<?php

namespace App\Traits;

use App\Models\MainCore\Branch;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasBranch
{
    /**
     * Get the branch that owns this model
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope a query to only include records for a specific branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to only include records for user's accessible branches
     */
    public function scopeForUserBranches($query, $user = null)
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return $query;
        }

        // If user has super_admin role, show all branches
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Get user's branch IDs
        $branchIds = $user->branches()->pluck('branches.id')->toArray();
        
        if (empty($branchIds)) {
            // If user has no branches, return empty result
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('branch_id', $branchIds);
    }
}

