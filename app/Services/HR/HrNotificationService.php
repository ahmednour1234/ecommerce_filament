<?php

namespace App\Services\HR;

use App\Models\HR\HrNotification;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Models\User;
use App\Models\MainCore\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class HrNotificationService
{
    /**
     * Create a new notification
     */
    public function createNotification(
        string $type,
        string $title,
        string $message,
        int $employeeId,
        ?string $relatedType = null,
        ?int $relatedId = null,
        ?string $actionUrl = null
    ): HrNotification {
        $employee = Employee::findOrFail($employeeId);

        return HrNotification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'employee_id' => $employeeId,
            'branch_id' => $employee->branch_id,
            'action_url' => $actionUrl,
            'created_by' => Auth::id(),
            'status' => 'unread',
        ]);
    }

    /**
     * Notify branch managers about a new request
     */
    public function notifyBranchManagers(
        string $type,
        string $title,
        string $message,
        int $employeeId,
        ?string $relatedType = null,
        ?int $relatedId = null,
        ?string $actionUrl = null
    ): HrNotification {
        return $this->createNotification(
            $type,
            $title,
            $message,
            $employeeId,
            $relatedType,
            $relatedId,
            $actionUrl
        );
    }

    /**
     * Notify general manager about a new request
     */
    public function notifyGeneralManager(
        string $type,
        string $title,
        string $message,
        int $employeeId,
        ?string $relatedType = null,
        ?int $relatedId = null,
        ?string $actionUrl = null
    ): HrNotification {
        return $this->createNotification(
            $type,
            $title,
            $message,
            $employeeId,
            $relatedType,
            $relatedId,
            $actionUrl
        );
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        $notification = HrNotification::findOrFail($notificationId);
        return $notification->markAsRead();
    }

    /**
     * Mark notification as action taken
     */
    public function markAsActionTaken(int $notificationId): bool
    {
        $notification = HrNotification::findOrFail($notificationId);
        return $notification->markAsActionTaken();
    }

    /**
     * Get unread count for current user based on permissions
     */
    public function getUnreadCount(?User $user = null): int
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return 0;
        }

        $query = HrNotification::unread();

        // Apply permission-based filtering
        $query = $this->applyPermissionFilter($query, $user);

        return $query->count();
    }

    /**
     * Get notifications for current user based on permissions
     */
    public function getNotifications(array $filters = [], ?User $user = null): Collection
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return collect([]);
        }

        $query = HrNotification::with(['employee', 'branch', 'creator']);

        // Apply permission-based filtering
        $query = $this->applyPermissionFilter($query, $user);

        // Apply filters
        if (isset($filters['type'])) {
            $query->ofType($filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['branch_id'])) {
            $query->forBranch($filters['branch_id']);
        }

        if (isset($filters['employee_id'])) {
            $query->forEmployee($filters['employee_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Apply permission-based filtering
     */
    protected function applyPermissionFilter($query, User $user)
    {
        // General manager can see all notifications
        if ($user->can('hr_notifications.view_all') || $user->hasRole('super_admin')) {
            return $query;
        }

        // Branch manager can see notifications for their branch only
        if ($user->can('hr_notifications.view_branch')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            $branchId = $user->branch_id ?? ($user->branch ? $user->branch->id : null);
            if (!empty($branchId)) {
                $branchIds[] = (int) $branchId;
            }
            $branchIds = array_values(array_unique(array_filter($branchIds)));

            if (!empty($branchIds)) {
                return $query->whereIn('branch_id', $branchIds);
            }

            // No assigned branches means access to all branches.
            return $query;
        }

        // Employee can see only their own notifications
        if ($user->employee) {
            return $query->forEmployee($user->employee->id);
        }

        // Default: no access
        return $query->whereRaw('1 = 0');
    }

    /**
     * Create leave request notification
     */
    public function notifyLeaveRequestCreated(Employee $employee, int $leaveRequestId): HrNotification
    {
        $title = 'طلب إجازة جديد';
        $message = "تم تقديم طلب إجازة جديد من الموظف {$employee->full_name}";
        $actionUrl = \App\Filament\Resources\HR\LeaveRequestResource::getUrl('edit', ['record' => $leaveRequestId]);

        return $this->notifyBranchManagers(
            'leave_request',
            $title,
            $message,
            $employee->id,
            LeaveRequest::class,
            $leaveRequestId,
            $actionUrl
        );
    }

    /**
     * Create loan notification
     */
    public function notifyLoanCreated(Employee $employee, int $loanId): HrNotification
    {
        $title = 'طلب سلفة جديد';
        $message = "تم تقديم طلب سلفة جديد من الموظف {$employee->full_name}";
        $actionUrl = \App\Filament\Resources\HR\LoanResource::getUrl('edit', ['record' => $loanId]);

        return $this->notifyBranchManagers(
            'loan',
            $title,
            $message,
            $employee->id,
            \App\Models\HR\Loan::class,
            $loanId,
            $actionUrl
        );
    }

    /**
     * Create excuse request notification
     */
    public function notifyExcuseRequestCreated(Employee $employee, int $excuseRequestId): HrNotification
    {
        $title = 'طلب استئذان جديد';
        $message = "تم تقديم طلب استئذان جديد من الموظف {$employee->full_name}";
        $actionUrl = \App\Filament\Resources\HR\ExcuseRequestResource::getUrl('edit', ['record' => $excuseRequestId]);

        return $this->notifyBranchManagers(
            'excuse_request',
            $title,
            $message,
            $employee->id,
            \App\Models\HR\ExcuseRequest::class,
            $excuseRequestId,
            $actionUrl
        );
    }
}
