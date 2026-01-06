<?php

namespace App\Policies\HR;

use App\Models\HR\WorkPlace;
use App\Models\User;

class WorkPlacePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_work_places.view_any');
    }

    public function view(User $user, WorkPlace $workPlace): bool
    {
        return $user->can('hr_work_places.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_work_places.create');
    }

    public function update(User $user, WorkPlace $workPlace): bool
    {
        return $user->can('hr_work_places.update');
    }

    public function delete(User $user, WorkPlace $workPlace): bool
    {
        return $user->can('hr_work_places.delete');
    }
}

