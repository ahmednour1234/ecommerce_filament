<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Finance\BranchTransaction;

class BranchTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('branch_tx.view_any');
    }

    public function view(User $user, BranchTransaction $tx): bool
    {
        if (! $user->can('branch_tx.view')) return false;

        if ($user->can('branch_tx.view_all_branches')) return true;

        return (int) $tx->branch_id === (int) $user->branch_id;
    }

    public function create(User $user): bool
    {
        return $user->can('branch_tx.create');
    }

    public function update(User $user, BranchTransaction $tx): bool
    {
        // غالباً تمنع تعديل بعد الموافقة:
        if (! $user->can('branch_tx.update')) return false;
        return $tx->status === 'pending';
    }

    public function delete(User $user, BranchTransaction $tx): bool
    {
        return $user->can('branch_tx.delete');
    }

    public function approve(User $user, BranchTransaction $tx): bool
    {
        return $user->can('branch_tx.approve') && $tx->status === 'pending';
    }

    public function reject(User $user, BranchTransaction $tx): bool
    {
        return $user->can('branch_tx.reject') && $tx->status === 'pending';
    }

    public function export(User $user): bool
    {
        return $user->can('branch_tx.export');
    }

    public function print(User $user): bool
    {
        return $user->can('branch_tx.print');
    }
}
