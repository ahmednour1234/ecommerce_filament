<?php

namespace App\Policies\HR;

use App\Models\HR\SalaryComponent;
use App\Models\User;

class SalaryComponentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_components.view_any');
    }

    public function view(User $user, SalaryComponent $salaryComponent): bool
    {
        return $user->can('hr_components.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_components.create');
    }

    public function update(User $user, SalaryComponent $salaryComponent): bool
    {
        return $user->can('hr_components.update');
    }

    public function delete(User $user, SalaryComponent $salaryComponent): bool
    {
        return $user->can('hr_components.delete');
    }
}
