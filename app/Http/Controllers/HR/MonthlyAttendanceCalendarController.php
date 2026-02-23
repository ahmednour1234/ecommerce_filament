<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Services\HR\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MonthlyAttendanceCalendarController extends Controller
{
    protected AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function getAttendanceData(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $employeeId = $request->input('employee_id');
        $year = $request->input('year');
        $month = $request->input('month');

        $data = $this->attendanceService->getEmployeeMonthlyAttendance($employeeId, $year, $month);

        $events = [];
        foreach ($data['days'] as $day) {
            $color = match ($day['status']) {
                'present' => '#10b981',
                'absent' => '#ef4444',
                'holiday' => '#f59e0b',
                'leave' => '#3b82f6',
                default => '#6b7280',
            };

            $title = match ($day['status']) {
                'present' => 'حضور',
                'absent' => 'غياب',
                'holiday' => 'عطلة',
                'leave' => 'إجازة',
                default => 'غير محدد',
            };

            if ($day['first_in']) {
                $title .= ' - دخول: ' . $day['first_in'];
            }
            if ($day['last_out']) {
                $title .= ' - خروج: ' . $day['last_out'];
            }

            $events[] = [
                'id' => $day['date'],
                'title' => $title,
                'start' => $day['date'],
                'allDay' => true,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $day['status'],
                    'first_in' => $day['first_in'],
                    'last_out' => $day['last_out'],
                    'worked_hours' => $day['worked_hours'],
                    'late_minutes' => $day['late_minutes'],
                    'overtime_minutes' => $day['overtime_minutes'],
                    'expected_start_time' => $day['expected_start_time'],
                    'expected_end_time' => $day['expected_end_time'],
                    'schedule_name' => $day['schedule_name'],
                ],
            ];
        }

        return response()->json([
            'employee' => $data['employee'],
            'month' => $data['month'],
            'year' => $data['year'],
            'events' => $events,
            'summary' => $data['summary'],
        ]);
    }
}
