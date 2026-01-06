<?php

namespace App\Policies\HR;

use App\Models\HR\Bank;
use App\Models\User;

class BankPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_banks.view_any');
    }

    public function view(User $user, Bank $bank): bool
    {
        return $user->can('hr_banks.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_banks.create');
    }

    public function update(User $user, Bank $bank): bool
    {
        return $user->can('hr_banks.update');
    }

    public function delete(User $user, Bank $bank): bool
    {
        return $user->can('hr_banks.delete');
    }
}

