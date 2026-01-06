<?php

namespace App\Policies\HR;

use App\Models\HR\Position;
use App\Models\User;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr_positions.view_any');
    }

    public function view(User $user, Position $position): bool
    {
        return $user->can('hr_positions.view');
    }

    public function create(User $user): bool
    {
        return $user->can('hr_positions.create');
    }

    public function update(User $user, Position $position): bool
    {
        return $user->can('hr_positions.update');
    }

    public function delete(User $user, Position $position): bool
    {
        return $user->can('hr_positions.delete');
    }
}

