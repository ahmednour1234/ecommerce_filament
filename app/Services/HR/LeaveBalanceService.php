<?php

namespace App\Services\HR;

use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveType;
use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use App\Repositories\HR\LeaveBalanceRepository;
use App\Repositories\HR\LeaveRequestRepository;
use Illuminate\Support\Facades\DB;

class LeaveBalanceService
{
    protected LeaveBalanceRepository $repository;
    protected LeaveRequestRepository $requestRepository;

    public function __construct(
        LeaveBalanceRepository $repository,
        LeaveRequestRepository $requestRepository
    ) {
        $this->repository = $repository;
        $this->requestRepository = $requestRepository;
    }

    /**
     * Get all leave balances
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get balance for specific employee, leave type, and year
     */
    public function getBalance(int $employeeId, int $leaveTypeId, int $year): ?LeaveBalance
    {
        return $this->repository->getBalance($employeeId, $leaveTypeId, $year);
    }

    /**
     * Get all balances for an employee
     */
    public function getForEmployee(int $employeeId, ?int $year = null)
    {
        return $this->repository->getForEmployee($employeeId, $year);
    }

    /**
     * Initialize balances for an employee for a year
     */
    public function initializeForEmployee(int $employeeId, int $year): void
    {
        $leaveTypes = LeaveType::active()->get();
        
        if ($leaveTypes->isEmpty()) {
            throw new \Exception('No active leave types found.');
        }
        
        $this->repository->initializeForEmployee($employeeId, $year, $leaveTypes);
    }

    /**
     * Recalculate balances for a specific year
     */
    public function recalculateForYear(int $year): void
    {
        DB::transaction(function () use ($year) {
            // Get all employees
            $employees = Employee::active()->get();
            $leaveTypes = LeaveType::active()->get();
            
            foreach ($employees as $employee) {
                foreach ($leaveTypes as $leaveType) {
                    // Get or create balance
                    $balance = $this->repository->getBalance($employee->id, $leaveType->id, $year);
                    
                    if (!$balance) {
                        $balance = $this->repository->createOrUpdate(
                            $employee->id,
                            $leaveType->id,
                            $year,
                            [
                                'quota' => $leaveType->allowed_days_per_year,
                                'used' => 0,
                                'remaining' => $leaveType->allowed_days_per_year,
                            ]
                        );
                    }
                    
                    // Calculate used days from approved requests
                    $approvedRequests = LeaveRequest::where('employee_id', $employee->id)
                        ->where('leave_type_id', $leaveType->id)
                        ->where('status', 'approved')
                        ->whereYear('start_date', $year)
                        ->sum('total_days');
                    
                    $balance->used = $approvedRequests;
                    $balance->updateRemaining();
                }
            }
        });
    }

    /**
     * Recalculate balances for a specific employee
     */
    public function recalculateForEmployee(int $employeeId, int $year): void
    {
        DB::transaction(function () use ($employeeId, $year) {
            $leaveTypes = LeaveType::active()->get();
            
            foreach ($leaveTypes as $leaveType) {
                // Get or create balance
                $balance = $this->repository->getBalance($employeeId, $leaveType->id, $year);
                
                if (!$balance) {
                    $balance = $this->repository->createOrUpdate(
                        $employeeId,
                        $leaveType->id,
                        $year,
                        [
                            'quota' => $leaveType->allowed_days_per_year,
                            'used' => 0,
                            'remaining' => $leaveType->allowed_days_per_year,
                        ]
                    );
                }
                
                // Calculate used days from approved requests
                $approvedRequests = LeaveRequest::where('employee_id', $employeeId)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', $year)
                    ->sum('total_days');
                
                $balance->used = $approvedRequests;
                $balance->updateRemaining();
            }
        });
    }

    /**
     * Update balance when a request is approved
     */
    public function updateOnApproval(LeaveRequest $leaveRequest): void
    {
        $year = $leaveRequest->start_date->year;
        $balance = $this->repository->getBalance(
            $leaveRequest->employee_id,
            $leaveRequest->leave_type_id,
            $year
        );
        
        if ($balance) {
            $balance->used += $leaveRequest->total_days;
            $balance->updateRemaining();
        }
    }

    /**
     * Restore balance when a request is cancelled
     */
    public function restoreOnCancellation(LeaveRequest $leaveRequest): void
    {
        $year = $leaveRequest->start_date->year;
        $balance = $this->repository->getBalance(
            $leaveRequest->employee_id,
            $leaveRequest->leave_type_id,
            $year
        );
        
        if ($balance) {
            $balance->used = max(0, $balance->used - $leaveRequest->total_days);
            $balance->updateRemaining();
        }
    }
}

