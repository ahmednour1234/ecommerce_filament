<?php

namespace App\Policies\HR;

use App\Models\HR\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_departments.view_any');
    }

    public function view(User $user, Department $department): bool
    {
        return $user->can('hr_departments.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_departments.create');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->can('hr_departments.update');
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->can('hr_departments.delete');
    }
}

