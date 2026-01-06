<?php

namespace App\Policies\HR;

use App\Models\HR\EmployeeGroup;
use App\Models\User;

class EmployeeGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_employee_groups.view_any');
    }

    public function view(User $user, EmployeeGroup $employeeGroup): bool
    {
        return $user->can('hr_employee_groups.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_employee_groups.create');
    }

    public function update(User $user, EmployeeGroup $employeeGroup): bool
    {
        return $user->can('hr_employee_groups.update');
    }

    public function delete(User $user, EmployeeGroup $employeeGroup): bool
    {
        return $user->can('hr_employee_groups.delete');
    }
}

