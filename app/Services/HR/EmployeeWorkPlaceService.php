<?php

namespace App\Services\HR;

use App\Models\HR\Employee;
use App\Models\HR\EmployeeWorkPlace;
use App\Models\HR\WorkPlace;
use Illuminate\Support\Facades\DB;

class EmployeeWorkPlaceService
{
    public function assignWorkPlace(int $employeeId, int $workPlaceId): EmployeeWorkPlace
    {
        return EmployeeWorkPlace::updateOrCreate(
            ['employee_id' => $employeeId],
            ['work_place_id' => $workPlaceId]
        );
    }

    public function bulkAssign(array $assignments): void
    {
        DB::transaction(function () use ($assignments) {
            foreach ($assignments as $employeeId => $workPlaceId) {
                if ($workPlaceId) {
                    $this->assignWorkPlace($employeeId, $workPlaceId);
                } else {
                    EmployeeWorkPlace::where('employee_id', $employeeId)->delete();
                }
            }
        });
    }

    public function removeAssignment(int $employeeId): bool
    {
        return EmployeeWorkPlace::where('employee_id', $employeeId)->delete();
    }

    public function getEmployeeWorkPlace(int $employeeId): ?EmployeeWorkPlace
    {
        return EmployeeWorkPlace::where('employee_id', $employeeId)->first();
    }

    public function getEmployeesByDepartment(int $departmentId)
    {
        return Employee::where('department_id', $departmentId)
            ->active()
            ->with(['workPlace.workPlace', 'department', 'position'])
            ->get();
    }
}

