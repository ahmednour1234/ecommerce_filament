<?php

namespace App\Policies\Recruitment;

use App\Models\Recruitment\RecruitmentContract;
use App\Models\User;

class RecruitmentContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('recruitment_contracts.view_any');
    }

    public function view(User $user, RecruitmentContract $contract): bool
    {
        return $user->hasRole('super_admin') || $user->can('recruitment_contracts.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('recruitment_contracts.create');
    }

    public function update(User $user, RecruitmentContract $contract): bool
    {
        return $user->hasRole('super_admin') || $user->can('recruitment_contracts.update');
    }

    public function delete(User $user, RecruitmentContract $contract): bool
    {
        return $user->hasRole('super_admin') || $user->can('recruitment_contracts.delete');
    }

    public function manageFinance(User $user, RecruitmentContract $contract): bool
    {
        return $user->hasRole('super_admin') || $user->can('recruitment_contracts.finance.manage');
    }

    public function updateStatus(User $user, RecruitmentContract $contract): bool
    {
        return $user->hasRole('super_admin') || $user->can('recruitment_contracts.status.update');
    }
}
