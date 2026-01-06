<?php

namespace App\Policies\HR;

use App\Models\HR\ExcuseRequest;
use App\Models\User;

class ExcuseRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_excuse_requests.view_any');
    }

    public function view(User $user, ExcuseRequest $excuseRequest): bool
    {
        return $user->can('hr_excuse_requests.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_excuse_requests.create');
    }

    public function update(User $user, ExcuseRequest $excuseRequest): bool
    {
        return $user->can('hr_excuse_requests.update');
    }

    public function delete(User $user, ExcuseRequest $excuseRequest): bool
    {
        return $user->can('hr_excuse_requests.delete');
    }

    public function approve(User $user, ExcuseRequest $excuseRequest): bool
    {
        return $user->can('hr_excuse_requests.approve');
    }

    public function reject(User $user, ExcuseRequest $excuseRequest): bool
    {
        return $user->can('hr_excuse_requests.reject');
    }
}

