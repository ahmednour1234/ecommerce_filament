<?php

namespace App\Services\Biometric;

use App\Models\Biometric\BiometricAttendance;
use App\Models\Biometric\BiometricDevice;

class BiometricLogService
{
    public function storeLogs(BiometricDevice $device, array $validated): array
    {
        $inserted = 0;
        $skipped = 0;

        foreach ($validated['logs'] as $log) {
            try {
                BiometricAttendance::create([
                    'device_id' => $device->id,
                    'user_id' => (string) $log['user_id'],
                    'attended_at' => $log['timestamp'],
                    'state' => $log['state'] ?? null,
                    'type' => $log['type'] ?? null,
                    'ip_address' => $validated['ip_address'] ?? null,
                    'raw_data' => $log,
                ]);
                $inserted++;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'UNIQUE constraint')) {
                    $skipped++;
                } else {
                    throw $e;
                }
            }
        }

        return [
            'inserted' => $inserted,
            'skipped' => $skipped,
        ];
    }
}
