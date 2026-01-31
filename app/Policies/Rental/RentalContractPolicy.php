<?php

namespace App\Policies\Rental;

use App\Models\Rental\RentalContract;
use App\Models\User;

class RentalContractPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.contracts.view_any');
    }

    public function view(User $user, RentalContract $contract): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.contracts.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.contracts.create');
    }

    public function update(User $user, RentalContract $contract): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.contracts.update');
    }

    public function delete(User $user, RentalContract $contract): bool
    {
        return $user->can('rental.contracts.delete');
    }

    public function restore(User $user, RentalContract $contract): bool
    {
        return $user->can('rental.contracts.restore');
    }

    public function forceDelete(User $user, RentalContract $contract): bool
    {
        return $user->can('rental.contracts.force_delete');
    }
}
