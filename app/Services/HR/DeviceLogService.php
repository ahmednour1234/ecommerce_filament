<?php

namespace App\Services\HR;

use App\Models\HR\AttendanceLog;
use App\Models\HR\Device;
use App\Models\HR\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DeviceLogService
{
    public function ingestDeviceLog(array $data, Device $device): AttendanceLog
    {
        $validated = $this->validate($data);

        // Find employee by fingerprint_device_id or employee_number
        $employee = $this->findEmployee($validated['employee_code']);

        if (!$employee) {
            throw new \Exception('Employee not found for code: ' . $validated['employee_code']);
        }

        // Determine log type
        $type = $validated['event'] ?? 'check_in';
        if ($type === 'auto') {
            // Auto-detect: if first log of day, it's check_in, otherwise check_out
            $lastLog = AttendanceLog::where('employee_id', $employee->id)
                ->whereDate('log_datetime', Carbon::parse($validated['timestamp'])->toDateString())
                ->latest()
                ->first();
            
            $type = $lastLog ? 'check_out' : 'check_in';
        }

        return AttendanceLog::create([
            'employee_id' => $employee->id,
            'log_datetime' => $validated['timestamp'],
            'type' => $type,
            'source' => 'device',
            'device_id' => $device->id,
            'raw_payload' => $validated['payload'] ?? null,
        ]);
    }

    protected function findEmployee(string $employeeCode): ?Employee
    {
        // First try fingerprint_device_id
        $employee = Employee::where('fingerprint_device_id', $employeeCode)->first();
        
        if ($employee) {
            return $employee;
        }

        // Fallback to employee_number
        return Employee::where('employee_number', $employeeCode)->first();
    }

    protected function validate(array $data): array
    {
        $rules = [
            'device_serial' => 'nullable|string',
            'employee_code' => 'required|string',
            'timestamp' => 'required|date',
            'event' => 'nullable|in:check_in,check_out,auto',
            'payload' => 'nullable|array',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

