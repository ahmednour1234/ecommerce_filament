<?php

namespace App\Services\HR;

use App\Repositories\HR\LeaveRequestRepository;
use Illuminate\Support\Collection;

class LeaveReportService
{
    protected LeaveRequestRepository $repository;

    public function __construct(LeaveRequestRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get report data with filters
     */
    public function getReportData(array $filters = []): array
    {
        $requests = $this->repository->getAll($filters);
        $statistics = $this->repository->getStatistics($filters);
        
        // Format data for display
        $rows = $requests->map(function ($request) {
            return [
                'id' => $request->id,
                'employee_number' => $request->employee->employee_number ?? '',
                'employee_name' => $request->employee->full_name ?? '',
                'department' => $request->employee->department->name ?? '',
                'leave_type' => $request->leaveType->name ?? '',
                'start_date' => $request->start_date->format('Y-m-d'),
                'end_date' => $request->end_date->format('Y-m-d'),
                'total_days' => $request->total_days,
                'status' => $request->status,
                'reason' => $request->reason,
                'created_at' => $request->created_at->format('Y-m-d H:i'),
            ];
        });
        
        return [
            'rows' => $rows,
            'statistics' => $statistics,
            'filters' => $filters,
        ];
    }

    /**
     * Get summary statistics
     */
    public function getSummary(array $filters = []): array
    {
        return $this->repository->getStatistics($filters);
    }

    /**
     * Get report data for export
     */
    public function getExportData(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }
}

