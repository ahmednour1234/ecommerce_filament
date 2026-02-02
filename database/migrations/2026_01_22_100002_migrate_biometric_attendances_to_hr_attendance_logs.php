<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('biometric_attendances')) {
            return;
        }

        if (!Schema::hasTable('hr_attendance_logs')) {
            return;
        }

        if (!Schema::hasTable('hr_employees')) {
            return;
        }

        if (!Schema::hasTable('hr_devices')) {
            return;
        }

        $deviceMapping = DB::table('biometric_devices')
            ->join('hr_devices', function ($join) {
                $join->on('biometric_devices.api_key', '=', 'hr_devices.api_key')
                    ->orOn('biometric_devices.serial_number', '=', 'hr_devices.serial_number');
            })
            ->pluck('hr_devices.id', 'biometric_devices.id')
            ->toArray();

        $attendances = DB::table('biometric_attendances')
            ->orderBy('attended_at')
            ->get();

        $employeeLogsByDate = [];

        foreach ($attendances as $attendance) {
            $hrDeviceId = $deviceMapping[$attendance->device_id] ?? null;

            $employee = DB::table('hr_employees')
                ->where('fingerprint_device_id', $attendance->user_id)
                ->orWhere('employee_number', $attendance->user_id)
                ->first();

            if (!$employee) {
                continue;
            }

            $date = date('Y-m-d', strtotime($attendance->attended_at));
            $key = $employee->id . '_' . $date;

            if (!isset($employeeLogsByDate[$key])) {
                $employeeLogsByDate[$key] = [];
            }

            $employeeLogsByDate[$key][] = [
                'employee_id' => $employee->id,
                'log_datetime' => $attendance->attended_at,
                'device_id' => $hrDeviceId,
                'raw_payload' => $attendance->raw_data ? json_encode($attendance->raw_data) : null,
            ];
        }

        foreach ($employeeLogsByDate as $logs) {
            usort($logs, function ($a, $b) {
                return strtotime($a['log_datetime']) - strtotime($b['log_datetime']);
            });

            foreach ($logs as $index => $log) {
                $existing = DB::table('hr_attendance_logs')
                    ->where('employee_id', $log['employee_id'])
                    ->where('log_datetime', $log['log_datetime'])
                    ->exists();

                if ($existing) {
                    continue;
                }

                $type = 'check_in';
                if ($index > 0) {
                    $prevLog = $logs[$index - 1];
                    $prevType = DB::table('hr_attendance_logs')
                        ->where('employee_id', $log['employee_id'])
                        ->where('log_datetime', $prevLog['log_datetime'])
                        ->value('type');

                    if ($prevType === 'check_in') {
                        $type = 'check_out';
                    } else {
                        $type = 'check_in';
                    }
                }

                DB::table('hr_attendance_logs')->insert([
                    'employee_id' => $log['employee_id'],
                    'log_datetime' => $log['log_datetime'],
                    'type' => $type,
                    'source' => 'api',
                    'device_id' => $log['device_id'],
                    'raw_payload' => $log['raw_payload'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
    }
};
