<?php

namespace App\Services\HR;

use App\Models\HR\Device;
use Illuminate\Support\Facades\Validator;

class DeviceService
{
    public function getAll(array $filters = [])
    {
        $query = Device::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query->orderBy('name')->get();
    }

    public function getActive()
    {
        return Device::active()->orderBy('name')->get();
    }

    public function create(array $data): Device
    {
        $validated = $this->validate($data);
        
        // Generate API key if not provided
        if (empty($validated['api_key'])) {
            $validated['api_key'] = $this->generateApiKey();
        }
        
        return Device::create($validated);
    }

    public function update(Device $device, array $data): Device
    {
        $validated = $this->validate($data, $device);
        
        $device->update($validated);
        
        return $device->fresh();
    }

    public function delete(Device $device): bool
    {
        return $device->delete();
    }

    public function toggleStatus(Device $device): Device
    {
        $device->update(['status' => !$device->status]);
        return $device->fresh();
    }

    public function findByApiKey(string $apiKey): ?Device
    {
        return Device::byApiKey($apiKey)->active()->first();
    }

    protected function generateApiKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    protected function validate(array $data, ?Device $device = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'type' => 'required|in:fingerprint',
            'ip' => 'nullable|ip',
            'serial_number' => 'nullable|string|max:255',
            'api_key' => 'nullable|string|max:255|unique:hr_devices,api_key' . ($device ? ',' . $device->id : ''),
            'status' => 'boolean',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

