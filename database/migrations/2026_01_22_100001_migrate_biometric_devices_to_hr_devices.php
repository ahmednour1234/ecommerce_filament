<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('biometric_devices')) {
            return;
        }

        if (!Schema::hasTable('hr_devices')) {
            return;
        }

        $biometricDevices = DB::table('biometric_devices')->get();

        foreach ($biometricDevices as $device) {
            $exists = DB::table('hr_devices')
                ->where('api_key', $device->api_key)
                ->orWhere(function ($query) use ($device) {
                    if ($device->serial_number) {
                        $query->where('serial_number', $device->serial_number);
                    }
                })
                ->exists();

            if (!$exists) {
                DB::table('hr_devices')->insert([
                    'name' => $device->name,
                    'type' => 'fingerprint',
                    'ip' => $device->ip_address,
                    'serial_number' => $device->serial_number,
                    'api_key' => $device->api_key,
                    'status' => $device->status,
                    'created_at' => $device->created_at,
                    'updated_at' => $device->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
    }
};
