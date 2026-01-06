<?php

namespace App\Repositories\HR;

use App\Models\HR\LeaveBalance;
use Illuminate\Database\Eloquent\Collection;

class LeaveBalanceRepository
{
    protected LeaveBalance $model;

    public function __construct(LeaveBalance $model)
    {
        $this->model = $model;
    }

    /**
     * Get all leave balances with optional filters
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery()->with(['employee', 'leaveType']);

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        if (isset($filters['year'])) {
            $query->where('year', $filters['year']);
        }

        return $query->orderBy('year', 'desc')->orderBy('employee_id')->get();
    }

    /**
     * Get balance for specific employee, leave type, and year
     */
    public function getBalance(int $employeeId, int $leaveTypeId, int $year): ?LeaveBalance
    {
        return $this->model->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year)
            ->first();
    }

    /**
     * Get all balances for an employee
     */
    public function getForEmployee(int $employeeId, ?int $year = null): Collection
    {
        $query = $this->model->where('employee_id', $employeeId)
            ->with(['leaveType']);

        if ($year) {
            $query->where('year', $year);
        }

        return $query->orderBy('year', 'desc')->get();
    }

    /**
     * Get all balances for a year
     */
    public function getForYear(int $year): Collection
    {
        return $this->model->where('year', $year)
            ->with(['employee', 'leaveType'])
            ->orderBy('employee_id')
            ->get();
    }

    /**
     * Create or update balance
     */
    public function createOrUpdate(int $employeeId, int $leaveTypeId, int $year, array $data): LeaveBalance
    {
        return $this->model->updateOrCreate(
            [
                'employee_id' => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
            ],
            $data
        );
    }

    /**
     * Update balance
     */
    public function update(LeaveBalance $balance, array $data): bool
    {
        return $balance->update($data);
    }

    /**
     * Delete balance
     */
    public function delete(LeaveBalance $balance): bool
    {
        return $balance->delete();
    }

    /**
     * Initialize balances for an employee for a year
     */
    public function initializeForEmployee(int $employeeId, int $year, array $leaveTypes): void
    {
        foreach ($leaveTypes as $leaveType) {
            $this->model->updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                ],
                [
                    'quota' => $leaveType->allowed_days_per_year,
                    'used' => 0,
                    'remaining' => $leaveType->allowed_days_per_year,
                ]
            );
        }
    }
}

