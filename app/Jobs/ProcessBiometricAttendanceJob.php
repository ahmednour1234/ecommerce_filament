<?php

namespace App\Jobs;

use App\Models\Biometric\BiometricAttendance;
use App\Models\HR\AttendanceLog;
use App\Models\HR\Employee;
use App\Services\HR\AttendanceService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessBiometricAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $batchSize = 100;

    public function handle(AttendanceService $attendanceService): void
    {
        $unprocessedLogs = BiometricAttendance::unprocessed()
            ->orderBy('attended_at')
            ->limit($this->batchSize)
            ->get();

        if ($unprocessedLogs->isEmpty()) {
            return;
        }

        foreach ($unprocessedLogs as $biometricLog) {
            try {
                DB::beginTransaction();

                $employee = $this->findEmployee($biometricLog->user_id);

                if (!$employee) {
                    Log::warning('Employee not found for biometric log', [
                        'user_id' => $biometricLog->user_id,
                        'biometric_attendance_id' => $biometricLog->id,
                    ]);
                    $biometricLog->update(['processed' => true]);
                    DB::commit();
                    continue;
                }

                $logType = $this->determineLogType($employee, $biometricLog);

                AttendanceLog::create([
                    'employee_id' => $employee->id,
                    'log_datetime' => $biometricLog->attended_at,
                    'type' => $logType,
                    'source' => 'api',
                    'device_id' => null,
                    'raw_payload' => $biometricLog->raw_data,
                ]);

                $date = Carbon::parse($biometricLog->attended_at)->format('Y-m-d');
                $attendanceService->aggregateEmployeeDay($employee, $date);

                $biometricLog->update(['processed' => true]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to process biometric attendance', [
                    'biometric_attendance_id' => $biometricLog->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }

    protected function findEmployee(string $userId): ?Employee
    {
        $employee = Employee::where('fingerprint_device_id', $userId)->first();

        if ($employee) {
            return $employee;
        }

        return Employee::where('employee_number', $userId)->first();
    }

    protected function determineLogType(Employee $employee, BiometricAttendance $biometricLog): string
    {
        $date = Carbon::parse($biometricLog->attended_at)->format('Y-m-d');

        $existingLogs = AttendanceLog::where('employee_id', $employee->id)
            ->whereDate('log_datetime', $date)
            ->orderBy('log_datetime')
            ->get();

        if ($existingLogs->isEmpty()) {
            return 'check_in';
        }

        $lastLog = $existingLogs->last();

        if ($lastLog->type === 'check_in') {
            return 'check_out';
        }

        return 'check_in';
    }
}
