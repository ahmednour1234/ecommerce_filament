<?php

namespace App\Policies\HR;

use App\Models\HR\LeaveBalance;
use App\Models\User;

class LeaveBalancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr.leave_balance.view');
    }

    public function view(User $user, LeaveBalance $leaveBalance): bool
    {
        return $user->can('hr.leave_balance.view');
    }

    public function recalculate(User $user): bool
    {
        return $user->can('hr.leave_balance.recalculate');
    }

    public function export(User $user): bool
    {
        return $user->can('hr.leave_balance.export');
    }
}

