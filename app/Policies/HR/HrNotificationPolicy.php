<?php

namespace App\Policies\HR;

use App\Models\HR\HrNotification;
use App\Models\User;

class HrNotificationPolicy
{
    /**
     * Determine if the user can view any notifications.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('hr_notifications.view_any') ||
               $user->can('hr_notifications.view_branch') ||
               $user->can('hr_notifications.view_own');
    }

    /**
     * Determine if the user can view the notification.
     */
    public function view(User $user, HrNotification $notification): bool
    {
        // General manager can view all
        if ($user->can('hr_notifications.view_all') || $user->hasRole('super_admin')) {
            return true;
        }

        // Branch manager can view notifications for their branch
        if ($user->can('hr_notifications.view_branch')) {
            $branchId = $user->branch_id ?? ($user->branch ? $user->branch->id : null);
            if ($branchId && $notification->branch_id === $branchId) {
                return true;
            }
        }

        // Employee can view their own notifications
        if ($user->can('hr_notifications.view_own') && $user->employee) {
            return $notification->employee_id === $user->employee->id;
        }

        return false;
    }

    /**
     * Determine if the user can create notifications.
     */
    public function create(User $user): bool
    {
        return $user->can('hr_notifications.create');
    }

    /**
     * Determine if the user can update the notification.
     */
    public function update(User $user, HrNotification $notification): bool
    {
        return $user->can('hr_notifications.update');
    }

    /**
     * Determine if the user can delete the notification.
     */
    public function delete(User $user, HrNotification $notification): bool
    {
        return $user->can('hr_notifications.delete');
    }

    /**
     * Determine if the user can mark notification as read.
     */
    public function markAsRead(User $user, HrNotification $notification): bool
    {
        return $this->view($user, $notification);
    }
}
