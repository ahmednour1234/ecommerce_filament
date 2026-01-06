<?php

namespace App\Policies\HR;

use App\Models\HR\WorkSchedule;
use App\Models\User;

class WorkSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_work_schedules.view_any');
    }

    public function view(User $user, WorkSchedule $workSchedule): bool
    {
        return $user->can('hr_work_schedules.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_work_schedules.create');
    }

    public function update(User $user, WorkSchedule $workSchedule): bool
    {
        return $user->can('hr_work_schedules.update');
    }

    public function delete(User $user, WorkSchedule $workSchedule): bool
    {
        return $user->can('hr_work_schedules.delete');
    }
}

