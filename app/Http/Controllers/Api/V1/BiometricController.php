<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBiometricLogsRequest;
use App\Services\HR\DeviceLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BiometricController extends Controller
{
    protected DeviceLogService $deviceLogService;

    public function __construct(DeviceLogService $deviceLogService)
    {
        $this->deviceLogService = $deviceLogService;
    }

    public function ping(Request $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        return response()->json([
            'status' => 'ok',
            'device_id' => $device->id,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function storeLogs(StoreBiometricLogsRequest $request): JsonResponse
    {
        $device = $request->attributes->get('device');

        Log::info('Biometric logs request received', [
            'device_id' => $device->id ?? null,
            'device_serial' => $device->serial_number ?? null,
            'raw_request_all' => $request->all(),
            'raw_request_json' => $request->getContent(),
            'headers' => $request->headers->all(),
            'ip_address' => $request->ip(),
        ]);

        $validated = $request->validated();

        Log::info('Biometric logs validated data', [
            'device_id' => $device->id ?? null,
            'validated_data' => $validated,
            'logs_count' => count($validated['logs'] ?? []),
        ]);

        $inserted = 0;
        $skipped = 0;

        foreach ($validated['logs'] as $index => $log) {
            Log::info('Processing biometric log entry', [
                'device_id' => $device->id ?? null,
                'log_index' => $index,
                'raw_log_entry' => $log,
                'log_keys' => array_keys($log),
            ]);

            try {
                $logData = [
                    'device_serial' => $validated['serial_number'] ?? null,
                    'employee_code' => (string) $log['user_id'],
                    'timestamp' => $log['timestamp'],
                    'event' => 'auto',
                    'payload' => [
                        'state' => $log['state'] ?? null,
                        'type' => $log['type'] ?? null,
                        'ip_address' => $validated['ip_address'] ?? null,
                        'raw_data' => $log,
                    ],
                ];

                Log::info('Biometric log data prepared', [
                    'device_id' => $device->id ?? null,
                    'log_index' => $index,
                    'prepared_log_data' => $logData,
                ]);

                $this->deviceLogService->ingestDeviceLog($logData, $device);
                $inserted++;

                Log::info('Biometric log inserted successfully', [
                    'device_id' => $device->id ?? null,
                    'log_index' => $index,
                    'employee_code' => $logData['employee_code'],
                ]);
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'Employee not found')) {
                    $skipped++;
                    Log::warning('Biometric log skipped - employee not found', [
                        'device_id' => $device->id,
                        'log_index' => $index,
                        'user_id' => $log['user_id'],
                        'timestamp' => $log['timestamp'],
                        'raw_log' => $log,
                    ]);
                } else {
                    Log::error('Biometric log storage failed', [
                        'device_id' => $device->id,
                        'log_index' => $index,
                        'user_id' => $log['user_id'],
                        'error' => $e->getMessage(),
                        'error_trace' => $e->getTraceAsString(),
                        'raw_log' => $log,
                    ]);
                    $skipped++;
                }
            }
        }

        Log::info('Biometric logs processing completed', [
            'device_id' => $device->id ?? null,
            'total_logs' => count($validated['logs'] ?? []),
            'inserted' => $inserted,
            'skipped' => $skipped,
        ]);

        return response()->json([
            'status' => 'success',
            'inserted' => $inserted,
            'skipped' => $skipped,
        ], 201);
    }
}
