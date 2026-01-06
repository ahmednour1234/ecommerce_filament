<?php

namespace App\Policies\HR;

use App\Models\HR\Device;
use App\Models\User;

class DevicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_devices.view_any');
    }

    public function view(User $user, Device $device): bool
    {
        return $user->can('hr_devices.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_devices.create');
    }

    public function update(User $user, Device $device): bool
    {
        return $user->can('hr_devices.update');
    }

    public function delete(User $user, Device $device): bool
    {
        return $user->can('hr_devices.delete');
    }
}

