<?php

namespace App\Policies\HR;

use App\Models\HR\IdentityType;
use App\Models\User;

class IdentityTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_identity_types.view_any');
    }

    public function view(User $user, IdentityType $identityType): bool
    {
        return $user->can('hr_identity_types.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_identity_types.create');
    }

    public function update(User $user, IdentityType $identityType): bool
    {
        return $user->can('hr_identity_types.update');
    }

    public function delete(User $user, IdentityType $identityType): bool
    {
        return $user->can('hr_identity_types.delete');
    }
}

