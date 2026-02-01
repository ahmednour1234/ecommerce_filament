<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BiometricController;
use Illuminate\Http\Request;

Route::prefix('v1/biometric')
    ->middleware(['device.key', 'throttle:60,1'])
    ->group(function () {
        Route::get('/ping', [BiometricController::class, 'ping']);
        Route::post('/logs', [BiometricController::class, 'storeLogs'])
            ->middleware(['throttle:30,1', 'max_request_size']);

        Route::get('/debug-auth', function (Request $request) {
            $device = $request->attributes->get('device');
            return response()->json([
                'found' => $device !== null,
                'device_id' => $device?->id,
                'status' => $device?->status,
                'key_tail' => substr($request->header('X-DEVICE-KEY', ''), -8),
            ]);
        });
    });
