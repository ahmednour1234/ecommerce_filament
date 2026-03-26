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

        // Get user's branch IDs from pivot + legacy branch_id.
        $branchIds = $user->branches()->pluck('branches.id')->toArray();
        if (!empty($user->branch_id)) {
            $branchIds[] = (int) $user->branch_id;
        }
        $branchIds = array_values(array_unique(array_filter($branchIds)));

        // No assigned branch means full access to all branches.
        if (empty($branchIds)) {
            return $query;
        }

        return $query->whereIn('branch_id', $branchIds);
    }
}

