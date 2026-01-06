<?php

namespace App\Policies\HR;

use App\Models\HR\LeaveType;
use App\Models\User;

class LeaveTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr.leave_types.view');
    }

    public function view(User $user, LeaveType $leaveType): bool
    {
        return $user->can('hr.leave_types.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr.leave_types.create');
    }

    public function update(User $user, LeaveType $leaveType): bool
    {
        return $user->can('hr.leave_types.update');
    }

    public function delete(User $user, LeaveType $leaveType): bool
    {
        return $user->can('hr.leave_types.delete');
    }

    public function export(User $user): bool
    {
        return $user->can('hr.leave_types.export');
    }
}

