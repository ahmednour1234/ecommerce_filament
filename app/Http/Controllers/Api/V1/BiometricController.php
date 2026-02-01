<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBiometricLogsRequest;
use App\Models\Biometric\BiometricDevice;
use App\Services\Biometric\BiometricLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BiometricController extends Controller
{
    protected BiometricLogService $logService;

    public function __construct(BiometricLogService $logService)
    {
        $this->logService = $logService;
    }

    protected function authenticateDevice(Request $request): ?BiometricDevice
    {
        $apiKey = $request->header('X-DEVICE-KEY');

        if (!$apiKey) {
            return null;
        }

        return BiometricDevice::byApiKey($apiKey)->active()->first();
    }

    public function ping(Request $request): JsonResponse
    {
        $device = $this->authenticateDevice($request);

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive device',
            ], 401);
        }

        return response()->json([
            'status' => 'ok',
            'device_id' => $device->id,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    public function storeLogs(StoreBiometricLogsRequest $request): JsonResponse
    {
        $device = $this->authenticateDevice($request);

        if (!$device) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or inactive device',
            ], 401);
        }

        try {
            $result = $this->logService->storeLogs($device, $request->validated());

            return response()->json([
                'status' => 'success',
                'inserted' => $result['inserted'],
                'skipped' => $result['skipped'],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Biometric log storage failed', [
                'device_id' => $device->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to store logs',
            ], 500);
        }
    }
}
