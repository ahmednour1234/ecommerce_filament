<?php

namespace App\Services\HR;

use App\Models\HR\WorkPlace;
use Illuminate\Support\Facades\Validator;

class WorkPlaceService
{
    public function getAll(array $filters = [])
    {
        $query = WorkPlace::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->get();
    }

    public function getActive()
    {
        return WorkPlace::active()->orderBy('name')->get();
    }

    public function create(array $data): WorkPlace
    {
        $validated = $this->validate($data);
        
        return WorkPlace::create($validated);
    }

    public function update(WorkPlace $workPlace, array $data): WorkPlace
    {
        $validated = $this->validate($data, $workPlace);
        
        $workPlace->update($validated);
        
        return $workPlace->fresh();
    }

    public function delete(WorkPlace $workPlace): bool
    {
        return $workPlace->delete();
    }

    public function toggleStatus(WorkPlace $workPlace): WorkPlace
    {
        $workPlace->update(['status' => !$workPlace->status]);
        return $workPlace->fresh();
    }

    protected function validate(array $data, ?WorkPlace $workPlace = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1',
            'status' => 'boolean',
            'default_schedule_id' => 'nullable|exists:hr_work_schedules,id',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

