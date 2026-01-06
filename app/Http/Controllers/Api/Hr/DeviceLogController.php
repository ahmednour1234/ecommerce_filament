<?php

namespace App\Http\Controllers\Api\Hr;

use App\Http\Controllers\Controller;
use App\Models\HR\Device;
use App\Services\HR\DeviceLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeviceLogController extends Controller
{
    protected DeviceLogService $deviceLogService;

    public function __construct(DeviceLogService $deviceLogService)
    {
        $this->deviceLogService = $deviceLogService;
    }

    /**
     * Handle device log push from fingerprint device
     * POST /api/hr/attendance/device-log
     * Headers: X-DEVICE-KEY
     */
    public function store(Request $request): JsonResponse
    {
        // Authenticate by X-DEVICE-KEY
        $apiKey = $request->header('X-DEVICE-KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Missing X-DEVICE-KEY header',
            ], 401);
        }

        $device = Device::byApiKey($apiKey)->active()->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive device',
            ], 401);
        }

        // Validate request
        $validated = $request->validate([
            'device_serial' => 'nullable|string',
            'employee_code' => 'required|string',
            'timestamp' => 'required|date',
            'event' => 'nullable|in:check_in,check_out,auto',
            'payload' => 'nullable|array',
        ]);

        try {
            $log = $this->deviceLogService->ingestDeviceLog($validated, $device);

            return response()->json([
                'success' => true,
                'message' => 'Log recorded successfully',
                'log_id' => $log->id,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Device log ingestion failed', [
                'device_id' => $device->id,
                'error' => $e->getMessage(),
                'data' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

