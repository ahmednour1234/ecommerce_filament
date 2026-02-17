<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ServiceTransfer;

class ServiceTransferPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('service_transfer.view');
    }

    public function view(User $user, ServiceTransfer $transfer): bool
    {
        return $user->can('service_transfer.view');
    }

    public function create(User $user): bool
    {
        return $user->can('service_transfer.create');
    }

    public function update(User $user, ServiceTransfer $transfer): bool
    {
        return $user->can('service_transfer.update');
    }

    public function delete(User $user, ServiceTransfer $transfer): bool
    {
        return $user->can('service_transfer.delete');
    }

    public function archive(User $user, ServiceTransfer $transfer): bool
    {
        return $user->can('service_transfer.archive');
    }

    public function refund(User $user, ServiceTransfer $transfer): bool
    {
        return $user->can('service_transfer.refund');
    }
}
