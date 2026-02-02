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
        $validated = $request->validated();

        $inserted = 0;
        $skipped = 0;

        foreach ($validated['logs'] as $log) {
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

                $this->deviceLogService->ingestDeviceLog($logData, $device);
                $inserted++;
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'Employee not found')) {
                    $skipped++;
                    Log::warning('Biometric log skipped - employee not found', [
                        'device_id' => $device->id,
                        'user_id' => $log['user_id'],
                        'timestamp' => $log['timestamp'],
                    ]);
                } else {
                    Log::error('Biometric log storage failed', [
                        'device_id' => $device->id,
                        'user_id' => $log['user_id'],
                        'error' => $e->getMessage(),
                    ]);
                    $skipped++;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'inserted' => $inserted,
            'skipped' => $skipped,
        ], 201);
    }
}
