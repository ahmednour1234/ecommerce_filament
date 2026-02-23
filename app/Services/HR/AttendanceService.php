<?php

namespace App\Services\HR;

use App\Models\HR\AttendanceLog;
use App\Models\HR\AttendanceDay;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeSchedule;
use App\Models\HR\WorkSchedule;
use App\Models\HR\Holiday;
use App\Models\HR\ExcuseRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public function aggregateForDate($date): void
    {
        $employees = Employee::active()->get();

        foreach ($employees as $employee) {
            $this->aggregateEmployeeDay($employee, $date);
        }
    }

    public function aggregateEmployeeDay(Employee $employee, $date): AttendanceDay
    {
        $logs = AttendanceLog::where('employee_id', $employee->id)
            ->whereDate('log_datetime', $date)
            ->orderBy('log_datetime')
            ->get();

        $firstIn = $logs->where('type', 'check_in')->first()?->log_datetime;
        $lastOut = $logs->where('type', 'check_out')->last()?->log_datetime;

        // Check if it's a holiday
        $isHoliday = Holiday::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();

        if ($isHoliday) {
            return AttendanceDay::updateOrCreate(
                ['employee_id' => $employee->id, 'date' => $date],
                [
                    'status' => 'holiday',
                    'first_in' => null,
                    'last_out' => null,
                    'worked_minutes' => 0,
                    'late_minutes' => 0,
                    'overtime_minutes' => 0,
                ]
            );
        }

        // Get employee schedule for this date
        $schedule = EmployeeSchedule::where('employee_id', $employee->id)
            ->forDate($date)
            ->latest()
            ->first();

        if (!$schedule) {
            // No schedule, mark as absent if no logs
            if (!$firstIn) {
                return AttendanceDay::updateOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date],
                    [
                        'status' => 'absent',
                        'first_in' => null,
                        'last_out' => null,
                        'worked_minutes' => 0,
                        'late_minutes' => 0,
                        'overtime_minutes' => 0,
                    ]
                );
            }
        }

        $workSchedule = $schedule?->schedule;
        
        // Calculate worked minutes
        $workedMinutes = 0;
        if ($firstIn && $lastOut) {
            $workedMinutes = Carbon::parse($firstIn)->diffInMinutes(Carbon::parse($lastOut));
        }

        // Calculate late minutes
        $lateMinutes = 0;
        if ($firstIn && $workSchedule) {
            $expectedStart = Carbon::parse($date . ' ' . $workSchedule->start_time);
            $actualStart = Carbon::parse($firstIn);
            $lateMinutes = max(0, $actualStart->diffInMinutes($expectedStart) - $workSchedule->late_grace_minutes);
        }

        // Calculate overtime minutes
        $overtimeMinutes = 0;
        if ($lastOut && $workSchedule) {
            $expectedEnd = Carbon::parse($date . ' ' . $workSchedule->end_time);
            $actualEnd = Carbon::parse($lastOut);
            $overtimeMinutes = max(0, $actualEnd->diffInMinutes($expectedEnd));
        }

        // Check for excuse requests
        $excuseRequest = ExcuseRequest::where('employee_id', $employee->id)
            ->where('date', $date)
            ->where('status', 'approved')
            ->first();

        if ($excuseRequest) {
            $workedMinutes -= ($excuseRequest->hours * 60);
        }

        // Determine status
        $status = 'absent';
        if ($firstIn) {
            $status = 'present';
        }

        return AttendanceDay::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $date],
            [
                'first_in' => $firstIn,
                'last_out' => $lastOut,
                'worked_minutes' => max(0, $workedMinutes),
                'late_minutes' => $lateMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'status' => $status,
            ]
        );
    }

    public function getMonthlyReport(int $year, int $month, ?int $departmentId = null, ?int $workPlaceId = null)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = AttendanceDay::with(['employee.department', 'employee.position'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        if ($workPlaceId) {
            $query->whereHas('employee.workPlace', function ($q) use ($workPlaceId) {
                $q->where('work_place_id', $workPlaceId);
            });
        }

        return $query->get()->groupBy('employee_id')->map(function ($days, $employeeId) {
            $employee = $days->first()->employee;
            return [
                'employee_id' => $employeeId,
                'employee_number' => $employee->employee_number,
                'employee_name' => $employee->full_name,
                'department' => $employee->department->name ?? '',
                'position' => $employee->position->title ?? '',
                'present_days' => $days->where('status', 'present')->count(),
                'absent_days' => $days->where('status', 'absent')->count(),
                'leave_days' => $days->where('status', 'leave')->count(),
                'holiday_days' => $days->where('status', 'holiday')->count(),
                'late_days' => $days->where('late_minutes', '>', 0)->count(),
                'total_worked_minutes' => $days->sum('worked_minutes'),
                'total_overtime_minutes' => $days->sum('overtime_minutes'),
                'total_late_minutes' => $days->sum('late_minutes'),
            ];
        })->values();
    }

    public function getEmployeeMonthlyAttendance(int $employeeId, int $year, int $month): array
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendanceDays = AttendanceDay::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        $employee = Employee::findOrFail($employeeId);
        $days = [];
        $summary = [
            'present_days' => 0,
            'absent_days' => 0,
            'holiday_days' => 0,
            'leave_days' => 0,
            'total_worked_minutes' => 0,
            'total_worked_hours' => 0,
            'total_late_minutes' => 0,
            'total_overtime_minutes' => 0,
            'average_worked_hours_per_day' => 0,
        ];

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('Y-m-d');
            $attendanceDay = $attendanceDays->firstWhere('date', $dateStr);

            $employeeSchedule = EmployeeSchedule::where('employee_id', $employeeId)
                ->forDate($dateStr)
                ->latest()
                ->first();

            $workSchedule = $employeeSchedule?->schedule;
            $expectedStartTime = null;
            $expectedEndTime = null;
            
            if ($workSchedule) {
                $startTimeStr = is_string($workSchedule->start_time) 
                    ? $workSchedule->start_time 
                    : $workSchedule->start_time->format('H:i:s');
                $endTimeStr = is_string($workSchedule->end_time) 
                    ? $workSchedule->end_time 
                    : $workSchedule->end_time->format('H:i:s');
                
                $expectedStartTime = Carbon::parse($dateStr . ' ' . substr($startTimeStr, 0, 5));
                $expectedEndTime = Carbon::parse($dateStr . ' ' . substr($endTimeStr, 0, 5));
            }

            $dayData = [
                'date' => $dateStr,
                'status' => $attendanceDay?->status ?? 'absent',
                'first_in' => $attendanceDay?->first_in ? $attendanceDay->first_in->format('H:i') : null,
                'last_out' => $attendanceDay?->last_out ? $attendanceDay->last_out->format('H:i') : null,
                'worked_minutes' => $attendanceDay?->worked_minutes ?? 0,
                'worked_hours' => round(($attendanceDay?->worked_minutes ?? 0) / 60, 2),
                'late_minutes' => $attendanceDay?->late_minutes ?? 0,
                'overtime_minutes' => $attendanceDay?->overtime_minutes ?? 0,
                'expected_start_time' => $expectedStartTime ? $expectedStartTime->format('H:i') : null,
                'expected_end_time' => $expectedEndTime ? $expectedEndTime->format('H:i') : null,
                'schedule_name' => $workSchedule?->name ?? null,
            ];

            $days[] = $dayData;

            if ($attendanceDay) {
                switch ($attendanceDay->status) {
                    case 'present':
                        $summary['present_days']++;
                        break;
                    case 'absent':
                        $summary['absent_days']++;
                        break;
                    case 'holiday':
                        $summary['holiday_days']++;
                        break;
                    case 'leave':
                        $summary['leave_days']++;
                        break;
                }

                $summary['total_worked_minutes'] += $attendanceDay->worked_minutes;
                $summary['total_late_minutes'] += $attendanceDay->late_minutes;
                $summary['total_overtime_minutes'] += $attendanceDay->overtime_minutes;
            } else {
                $summary['absent_days']++;
            }

            $currentDate->addDay();
        }

        $summary['total_worked_hours'] = round($summary['total_worked_minutes'] / 60, 2);
        $presentDaysCount = $summary['present_days'];
        $summary['average_worked_hours_per_day'] = $presentDaysCount > 0 
            ? round($summary['total_worked_hours'] / $presentDaysCount, 2) 
            : 0;

        return [
            'employee' => [
                'id' => $employee->id,
                'employee_number' => $employee->employee_number,
                'full_name' => $employee->full_name,
            ],
            'month' => $month,
            'year' => $year,
            'days' => $days,
            'summary' => $summary,
        ];
    }
}

