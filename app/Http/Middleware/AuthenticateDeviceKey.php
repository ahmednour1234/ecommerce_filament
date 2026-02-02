<?php

namespace App\Http\Middleware;

use App\Models\HR\Device;
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
            Log::warning('Biometric auth: Missing X-DEVICE-KEY header');
            return response()->json([
                'status' => 'error',
                'message' => 'Missing X-DEVICE-KEY',
            ], 401);
        }

        $apiKey = trim($apiKey);

        if (config('app.env') !== 'production') {
            Log::info('Biometric auth attempt', [
                'key_prefix' => substr($apiKey, 0, 8),
            ]);
        }

        $device = Device::where('api_key', $apiKey)->first();

        if (!$device) {
            Log::warning('Biometric auth: Device not found', [
                'key_tail' => substr($apiKey, -8),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid device key',
            ], 401);
        }

        if ($device->status !== true) {
            Log::warning('Biometric auth: Device is inactive', [
                'device_id' => $device->id,
                'key_tail' => substr($apiKey, -8),
                'status' => $device->status,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Device is inactive',
            ], 403);
        }

        if (config('app.env') !== 'production') {
            Log::info('Biometric auth: Success', [
                'key_prefix' => substr($apiKey, 0, 8),
                'device_id' => $device->id,
                'status' => $device->status,
            ]);
        }

        $request->attributes->set('device', $device);

        return $next($request);
    }
}
