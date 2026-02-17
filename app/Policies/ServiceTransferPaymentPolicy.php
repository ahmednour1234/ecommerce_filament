<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ServiceTransferPayment;

class ServiceTransferPaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('service_transfer.view');
    }

    public function view(User $user, ServiceTransferPayment $payment): bool
    {
        return $user->can('service_transfer.view');
    }

    public function create(User $user): bool
    {
        return $user->can('service_transfer.payments.create');
    }

    public function update(User $user, ServiceTransferPayment $payment): bool
    {
        return $user->can('service_transfer.payments.create');
    }

    public function delete(User $user, ServiceTransferPayment $payment): bool
    {
        return $user->can('service_transfer.payments.delete');
    }
}
