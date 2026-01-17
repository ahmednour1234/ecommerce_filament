<?php

namespace App\Policies\HR;

use App\Models\HR\EmployeeFinancialProfile;
use App\Models\User;

class EmployeeFinancialProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_employee_financial.view_any');
    }

    public function view(User $user, EmployeeFinancialProfile $profile): bool
    {
        return $user->can('hr_employee_financial.view');
    }

    public function update(User $user, EmployeeFinancialProfile $profile): bool
    {
        return $user->can('hr_employee_financial.update');
    }
}
