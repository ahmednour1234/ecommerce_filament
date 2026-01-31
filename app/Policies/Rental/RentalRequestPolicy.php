<?php

namespace App\Policies\Rental;

use App\Models\Rental\RentalContractRequest;
use App\Models\User;

class RentalRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.requests.view_any');
    }

    public function view(User $user, RentalContractRequest $request): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.requests.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.requests.manage');
    }

    public function update(User $user, RentalContractRequest $request): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.requests.manage');
    }

    public function delete(User $user, RentalContractRequest $request): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.requests.manage');
    }

    public function convert(User $user, RentalContractRequest $request): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.requests.convert');
    }
}
