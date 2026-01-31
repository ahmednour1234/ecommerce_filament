<?php

namespace App\Policies\Rental;

use App\Models\Rental\RentalCancelRefundRequest;
use App\Models\User;

class CancelRefundRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.cancel_refund.view_any');
    }

    public function view(User $user, RentalCancelRefundRequest $request): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.cancel_refund.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.cancel_refund.manage');
    }

    public function update(User $user, RentalCancelRefundRequest $request): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.cancel_refund.manage');
    }

    public function delete(User $user, RentalCancelRefundRequest $request): bool
    {
        return $user->hasRole('super_admin') || $user->can('rental.cancel_refund.manage');
    }
}
