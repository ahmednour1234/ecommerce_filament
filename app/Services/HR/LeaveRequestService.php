<?php

namespace App\Services\HR;

use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveBalance;
use App\Repositories\HR\LeaveRequestRepository;
use App\Repositories\HR\LeaveBalanceRepository;
use App\Services\HR/HrNotificationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LeaveRequestService
{
    protected LeaveRequestRepository $repository;
    protected LeaveBalanceRepository $balanceRepository;
    protected HrNotificationService $notificationService;

    public function __construct(
        LeaveRequestRepository $repository,
        LeaveBalanceRepository $balanceRepository,
        HrNotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->balanceRepository = $balanceRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * Get all leave requests
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get paginated leave requests
     */
    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    /**
     * Get requests for a specific employee
     */
    public function getForEmployee(int $employeeId, array $filters = [])
    {
        return $this->repository->getForEmployee($employeeId, $filters);
    }

    /**
     * Find leave request by ID
     */
    public function findById(int $id): ?LeaveRequest
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new leave request
     */
    public function create(array $data): LeaveRequest
    {
        $validated = $this->validate($data);
        
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        
        // Calculate total days
        $totalDays = $this->calculateTotalDays($startDate, $endDate);
        $validated['total_days'] = $totalDays;
        
        // Check for overlapping requests
        if ($this->repository->hasOverlapping($validated['employee_id'], $startDate, $endDate)) {
            throw new \Exception('Leave request overlaps with an existing approved or pending request.');
        }
        
        // Check balance
        $year = $startDate->year;
        $balance = $this->balanceRepository->getBalance(
            $validated['employee_id'],
            $validated['leave_type_id'],
            $year
        );
        
        if (!$balance) {
            throw new \Exception('Leave balance not initialized for this employee and leave type.');
        }
        
        if ($balance->remaining < $totalDays) {
            throw new \Exception("Insufficient leave balance. Available: {$balance->remaining} days, Requested: {$totalDays} days.");
        }
        
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';
        
        $leaveRequest = $this->repository->create($validated);
        
        // Send notification to branch managers
        $employee = \App\Models\HR\Employee::findOrFail($validated['employee_id']);
        $this->notificationService->notifyLeaveRequestCreated($employee, $leaveRequest->id);
        
        return $leaveRequest;
    }

    /**
     * Update leave request
     */
    public function update(LeaveRequest $leaveRequest, array $data): LeaveRequest
    {
        if ($leaveRequest->status !== 'pending') {
            throw new \Exception('Only pending requests can be updated.');
        }
        
        $validated = $this->validate($data, $leaveRequest);
        
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        
        // Calculate total days
        $totalDays = $this->calculateTotalDays($startDate, $endDate);
        $validated['total_days'] = $totalDays;
        
        // Check for overlapping requests (excluding current)
        if ($this->repository->hasOverlapping($validated['employee_id'], $startDate, $endDate, $leaveRequest->id)) {
            throw new \Exception('Leave request overlaps with an existing approved or pending request.');
        }
        
        // Check balance if dates or leave type changed
        if ($validated['start_date'] != $leaveRequest->start_date->format('Y-m-d') ||
            $validated['end_date'] != $leaveRequest->end_date->format('Y-m-d') ||
            $validated['leave_type_id'] != $leaveRequest->leave_type_id) {
            
            $year = $startDate->year;
            $balance = $this->balanceRepository->getBalance(
                $validated['employee_id'],
                $validated['leave_type_id'],
                $year
            );
            
            if (!$balance) {
                throw new \Exception('Leave balance not initialized for this employee and leave type.');
            }
            
            // Add back old days if leave type changed
            if ($validated['leave_type_id'] != $leaveRequest->leave_type_id) {
                $oldYear = $leaveRequest->start_date->year;
                $oldBalance = $this->balanceRepository->getBalance(
                    $leaveRequest->employee_id,
                    $leaveRequest->leave_type_id,
                    $oldYear
                );
                if ($oldBalance) {
                    $oldBalance->used = max(0, $oldBalance->used - $leaveRequest->total_days);
                    $oldBalance->updateRemaining();
                }
            }
            
            if ($balance->remaining < $totalDays) {
                throw new \Exception("Insufficient leave balance. Available: {$balance->remaining} days, Requested: {$totalDays} days.");
            }
        }
        
        $validated['updated_by'] = Auth::id();
        
        $this->repository->update($leaveRequest, $validated);
        
        return $leaveRequest->fresh();
    }

    /**
     * Approve leave request by branch manager
     */
    public function approveByBranchManager(LeaveRequest $leaveRequest, ?string $managerNote = null): LeaveRequest
    {
        if ($leaveRequest->status !== 'pending') {
            throw new \Exception('Only pending requests can be approved by branch manager.');
        }
        
        $leaveRequest->update([
            'status' => 'branch_manager_approved',
            'branch_manager_approved_by' => Auth::id(),
            'branch_manager_approved_at' => now(),
            'manager_note' => $managerNote,
            'updated_by' => Auth::id(),
        ]);
        
        // Notify general manager
        $employee = $leaveRequest->employee;
        $this->notificationService->notifyGeneralManager(
            'leave_request',
            'طلب إجازة يحتاج موافقة المدير العام',
            "تمت موافقة مدير الفرع على طلب إجازة من الموظف {$employee->full_name} ويحتاج موافقة المدير العام",
            $employee->id,
            LeaveRequest::class,
            $leaveRequest->id,
            \App\Filament\Resources\HR\LeaveRequestResource::getUrl('edit', ['record' => $leaveRequest->id])
        );
        
        return $leaveRequest->fresh();
    }

    /**
     * Approve leave request by general manager
     */
    public function approveByGeneralManager(LeaveRequest $leaveRequest, ?string $managerNote = null): LeaveRequest
    {
        if ($leaveRequest->status !== 'branch_manager_approved') {
            throw new \Exception('Only branch manager approved requests can be approved by general manager.');
        }
        
        DB::transaction(function () use ($leaveRequest, $managerNote) {
            $leaveRequest->update([
                'status' => 'approved',
                'general_manager_approved_by' => Auth::id(),
                'general_manager_approved_at' => now(),
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'manager_note' => $managerNote,
                'updated_by' => Auth::id(),
            ]);
            
            // Update balance
            $year = $leaveRequest->start_date->year;
            $balance = $this->balanceRepository->getBalance(
                $leaveRequest->employee_id,
                $leaveRequest->leave_type_id,
                $year
            );
            
            if ($balance) {
                $balance->used += $leaveRequest->total_days;
                $balance->updateRemaining();
            }
        });
        
        return $leaveRequest->fresh();
    }

    /**
     * Approve leave request (backward compatibility - for direct approval)
     */
    public function approve(LeaveRequest $leaveRequest, ?string $managerNote = null): LeaveRequest
    {
        // If user has permission to approve directly, use general manager approval
        if (Auth::user()?->can('hr.leave_requests.approve_direct')) {
            return $this->approveByGeneralManager($leaveRequest, $managerNote);
        }
        
        // Otherwise, use branch manager approval
        return $this->approveByBranchManager($leaveRequest, $managerNote);
    }

    /**
     * Reject leave request
     */
    public function reject(LeaveRequest $leaveRequest, ?string $managerNote = null): LeaveRequest
    {
        if ($leaveRequest->status !== 'pending') {
            throw new \Exception('Only pending requests can be rejected.');
        }
        
        $leaveRequest->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'manager_note' => $managerNote,
            'updated_by' => Auth::id(),
        ]);
        
        return $leaveRequest->fresh();
    }

    /**
     * Cancel leave request
     */
    public function cancel(LeaveRequest $leaveRequest): LeaveRequest
    {
        if (!in_array($leaveRequest->status, ['pending', 'approved'])) {
            throw new \Exception('Only pending or approved requests can be cancelled.');
        }
        
        DB::transaction(function () use ($leaveRequest) {
            $wasApproved = $leaveRequest->status === 'approved';
            
            $leaveRequest->update([
                'status' => 'cancelled',
                'cancelled_by' => Auth::id(),
                'cancelled_at' => now(),
                'updated_by' => Auth::id(),
            ]);
            
            // If it was approved, restore balance
            if ($wasApproved) {
                $year = $leaveRequest->start_date->year;
                $balance = $this->balanceRepository->getBalance(
                    $leaveRequest->employee_id,
                    $leaveRequest->leave_type_id,
                    $year
                );
                
                if ($balance) {
                    $balance->used = max(0, $balance->used - $leaveRequest->total_days);
                    $balance->updateRemaining();
                }
            }
        });
        
        return $leaveRequest->fresh();
    }

    /**
     * Delete leave request
     */
    public function delete(LeaveRequest $leaveRequest): bool
    {
        if ($leaveRequest->status !== 'pending') {
            throw new \Exception('Only pending requests can be deleted.');
        }
        
        return $this->repository->delete($leaveRequest);
    }

    /**
     * Get statistics
     */
    public function getStatistics(array $filters = []): array
    {
        return $this->repository->getStatistics($filters);
    }

    /**
     * Calculate total days between two dates
     */
    protected function calculateTotalDays(Carbon $startDate, Carbon $endDate): int
    {
        if ($endDate->lt($startDate)) {
            throw new \Exception('End date must be after start date.');
        }
        
        // Calculate days including both start and end dates
        return $startDate->diffInDays($endDate) + 1;
    }

    /**
     * Validate leave request data
     */
    protected function validate(array $data, ?LeaveRequest $leaveRequest = null): array
    {
        $rules = [
            'employee_id' => 'required|exists:hr_employees,id',
            'leave_type_id' => 'required|exists:hr_leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment_path' => 'nullable|string|max:500',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

