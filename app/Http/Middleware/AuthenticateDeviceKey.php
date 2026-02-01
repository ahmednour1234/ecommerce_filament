<?php

namespace App\Http\Middleware;

use App\Models\Biometric\BiometricDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateDeviceKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-DEVICE-KEY');

        if (!$apiKey) {
            Log::warning('DeviceKey Auth: Missing header');
            return response()->json([
                'status' => 'error',
                'message' => 'Missing X-DEVICE-KEY',
            ], 401);
        }

        $apiKey = trim($apiKey);

        $device = BiometricDevice::where('api_key', $apiKey)->first();

        if (!$device) {
            Log::warning('DeviceKey Auth: Invalid key', [
                'key_tail' => substr($apiKey, -8),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device key',
            ], 401);
        }

        if (!$device->status) {
            Log::warning('DeviceKey Auth: Inactive device', [
                'device_id' => $device->id,
                'key_tail' => substr($apiKey, -8),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Device is inactive',
            ], 403);
        }

        Log::info('DeviceKey Auth', [
            'key_tail' => substr($apiKey, -8),
            'device_id' => $device->id,
            'status' => $device->status,
        ]);

        $request->attributes->set('device', $device);

        return $next($request);
    }
}
