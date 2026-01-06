<?php

namespace App\Services\HR;

use App\Models\HR\WorkPlace;
use App\Models\HR\EmployeeGroup;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeSchedule;
use App\Services\HR\EmployeeScheduleService;

class ScheduleCopyService
{
    protected EmployeeScheduleService $employeeScheduleService;

    public function __construct(EmployeeScheduleService $employeeScheduleService)
    {
        $this->employeeScheduleService = $employeeScheduleService;
    }

    public function copyFromWorkPlace(int $workPlaceId, array $employeeIds, string $dateFrom, ?string $dateTo = null): void
    {
        $workPlace = WorkPlace::findOrFail($workPlaceId);
        
        if (!$workPlace->default_schedule_id) {
            throw new \Exception('Work place does not have a default schedule');
        }

        $this->employeeScheduleService->bulkAssign(
            $employeeIds,
            $workPlace->default_schedule_id,
            $dateFrom,
            $dateTo
        );
    }

    public function copyFromGroup(int $groupId, array $employeeIds, string $dateFrom, ?string $dateTo = null): void
    {
        $group = EmployeeGroup::findOrFail($groupId);
        
        if (!$group->default_schedule_id) {
            throw new \Exception('Group does not have a default schedule');
        }

        $this->employeeScheduleService->bulkAssign(
            $employeeIds,
            $group->default_schedule_id,
            $dateFrom,
            $dateTo
        );
    }

    public function copyFromEmployee(int $sourceEmployeeId, array $targetEmployeeIds, string $dateFrom, ?string $dateTo = null): void
    {
        $latestSchedule = $this->employeeScheduleService->getLatestSchedule($sourceEmployeeId);
        
        if (!$latestSchedule) {
            throw new \Exception('Source employee does not have a schedule');
        }

        $this->employeeScheduleService->bulkAssign(
            $targetEmployeeIds,
            $latestSchedule->schedule_id,
            $dateFrom,
            $dateTo
        );
    }
}

