<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreBiometricLogsRequest;
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
