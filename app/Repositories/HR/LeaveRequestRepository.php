<?php

namespace App\Repositories\HR;

use App\Models\HR\LeaveRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class LeaveRequestRepository
{
    protected LeaveRequest $model;

    public function __construct(LeaveRequest $model)
    {
        $this->model = $model;
    }

    /**
     * Get all leave requests with optional filters
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

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        if (isset($filters['year'])) {
            $query->whereYear('start_date', $filters['year']);
        }

        if (isset($filters['month'])) {
            $query->whereMonth('start_date', $filters['month']);
        }

        if (isset($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    /**
     * Get paginated leave requests
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['employee', 'leaveType', 'employee.department']);

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['leave_type_id'])) {
            $query->where('leave_type_id', $filters['leave_type_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        if (isset($filters['year'])) {
            $query->whereYear('start_date', $filters['year']);
        }

        if (isset($filters['month'])) {
            $query->whereMonth('start_date', $filters['month']);
        }

        if (isset($filters['department_id'])) {
            $query->whereHas('employee', function ($q) use ($filters) {
                $q->where('department_id', $filters['department_id']);
            });
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('start_date', 'desc')->paginate($perPage);
    }

    /**
     * Get requests for a specific employee
     */
    public function getForEmployee(int $employeeId, array $filters = []): Collection
    {
        $query = $this->model->newQuery()
            ->where('employee_id', $employeeId)
            ->with(['leaveType']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    /**
     * Find leave request by ID
     */
    public function findById(int $id): ?LeaveRequest
    {
        return $this->model->with(['employee', 'leaveType', 'approver', 'rejector', 'canceller'])->find($id);
    }

    /**
     * Check for overlapping requests
     */
    public function hasOverlapping(int $employeeId, Carbon $startDate, Carbon $endDate, ?int $excludeId = null): bool
    {
        return $this->model->overlapping($employeeId, $startDate, $endDate, $excludeId)->exists();
    }

    /**
     * Get overlapping requests
     */
    public function getOverlapping(int $employeeId, Carbon $startDate, Carbon $endDate, ?int $excludeId = null): Collection
    {
        return $this->model->overlapping($employeeId, $startDate, $endDate, $excludeId)->get();
    }

    /**
     * Create a new leave request
     */
    public function create(array $data): LeaveRequest
    {
        return $this->model->create($data);
    }

    /**
     * Update leave request
     */
    public function update(LeaveRequest $leaveRequest, array $data): bool
    {
        return $leaveRequest->update($data);
    }

    /**
     * Delete leave request
     */
    public function delete(LeaveRequest $leaveRequest): bool
    {
        return $leaveRequest->delete();
    }

    /**
     * Get statistics for leave requests
     */
    public function getStatistics(array $filters = []): array
    {
        $query = $this->model->newQuery();

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['year'])) {
            $query->whereYear('start_date', $filters['year']);
        }

        if (isset($filters['date_from'])) {
            $query->where('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('end_date', '<=', $filters['date_to']);
        }

        return [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
        ];
    }
}

