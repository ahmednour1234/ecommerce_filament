<?php

namespace App\Policies\HR;

use App\Models\HR\BloodType;
use App\Models\User;

class BloodTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_blood_types.view_any');
    }

    public function view(User $user, BloodType $bloodType): bool
    {
        return $user->can('hr_blood_types.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_blood_types.create');
    }

    public function update(User $user, BloodType $bloodType): bool
    {
        return $user->can('hr_blood_types.update');
    }

    public function delete(User $user, BloodType $bloodType): bool
    {
        return $user->can('hr_blood_types.delete');
    }
}

