<?php

namespace App\Services\HR;

use App\Models\HR\EmployeeSchedule;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\Validator;

class EmployeeScheduleService
{
    public function assignSchedule(int $employeeId, int $scheduleId, string $dateFrom, ?string $dateTo = null): EmployeeSchedule
    {
        $validated = $this->validate([
            'employee_id' => $employeeId,
            'schedule_id' => $scheduleId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);

        return EmployeeSchedule::create($validated);
    }

    public function bulkAssign(array $employeeIds, int $scheduleId, string $dateFrom, ?string $dateTo = null): void
    {
        foreach ($employeeIds as $employeeId) {
            $this->assignSchedule($employeeId, $scheduleId, $dateFrom, $dateTo);
        }
    }

    public function getEmployeeSchedule(int $employeeId, $date): ?EmployeeSchedule
    {
        return EmployeeSchedule::where('employee_id', $employeeId)
            ->forDate($date)
            ->latest()
            ->first();
    }

    public function getLatestSchedule(int $employeeId): ?EmployeeSchedule
    {
        return EmployeeSchedule::where('employee_id', $employeeId)
            ->latest()
            ->first();
    }

    protected function validate(array $data): array
    {
        $rules = [
            'employee_id' => 'required|exists:hr_employees,id',
            'schedule_id' => 'required|exists:hr_work_schedules,id',
            'date_from' => 'required|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

