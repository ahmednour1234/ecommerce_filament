<?php

namespace App\Policies\HR;

use App\Models\HR\PayrollRun;
use App\Models\User;

class PayrollRunPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_payroll.view_any');
    }

    public function view(User $user, PayrollRun $payrollRun): bool
    {
        return $user->can('hr_payroll.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_payroll.create');
    }

    public function approve(User $user, PayrollRun $payrollRun): bool
    {
        return $user->can('hr_payroll.approve');
    }

    public function pay(User $user, PayrollRun $payrollRun): bool
    {
        return $user->can('hr_payroll.pay');
    }

    public function export(User $user, PayrollRun $payrollRun): bool
    {
        return $user->can('hr_payroll.export');
    }
}
