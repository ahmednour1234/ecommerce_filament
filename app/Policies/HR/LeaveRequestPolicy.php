<?php

namespace App\Policies\HR;

use App\Models\HR\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('hr.leave_requests.view_any');
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        // Users can view their own requests or if they have view_any permission
        if ($user->can('hr.leave_requests.view_any')) {
            return true;
        }
        
        return $user->can('hr.leave_requests.view_own') && 
               $this->isOwner($user, $leaveRequest);
    }

    public function create(User $user): bool
    {
        return $user->can('hr.leave_requests.create');
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        // Only pending requests can be updated
        if ($leaveRequest->status !== 'pending') {
            return false;
        }
        
        // Users can update their own requests or if they have update permission
        if ($user->can('hr.leave_requests.update')) {
            return true;
        }
        
        return $this->isOwner($user, $leaveRequest);
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        // Only pending requests can be deleted
        if ($leaveRequest->status !== 'pending') {
            return false;
        }
        
        // Users can delete their own requests or if they have delete permission
        if ($user->can('hr.leave_requests.delete')) {
            return true;
        }
        
        return $this->isOwner($user, $leaveRequest);
    }

    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('hr.leave_requests.approve');
    }

    public function reject(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('hr.leave_requests.reject');
    }

    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->can('hr.leave_requests.cancel');
    }

    public function export(User $user): bool
    {
        return $user->can('hr.leave_requests.export');
    }

    /**
     * Check if user is the owner of the leave request
     */
    protected function isOwner(User $user, LeaveRequest $leaveRequest): bool
    {
        // This assumes there's a relationship between User and Employee
        // You may need to adjust this based on your actual user-employee relationship
        // For now, we'll check if created_by matches
        return $leaveRequest->created_by === $user->id;
    }
}

