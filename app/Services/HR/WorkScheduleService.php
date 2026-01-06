<?php

namespace App\Services\HR;

use App\Models\HR\WorkSchedule;
use Illuminate\Support\Facades\Validator;

class WorkScheduleService
{
    public function getAll(array $filters = [])
    {
        $query = WorkSchedule::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->get();
    }

    public function getActive()
    {
        return WorkSchedule::active()->orderBy('name')->get();
    }

    public function create(array $data): WorkSchedule
    {
        $validated = $this->validate($data);
        
        return WorkSchedule::create($validated);
    }

    public function update(WorkSchedule $schedule, array $data): WorkSchedule
    {
        $validated = $this->validate($data, $schedule);
        
        $schedule->update($validated);
        
        return $schedule->fresh();
    }

    public function delete(WorkSchedule $schedule): bool
    {
        return $schedule->delete();
    }

    public function toggleStatus(WorkSchedule $schedule): WorkSchedule
    {
        $schedule->update(['status' => !$schedule->status]);
        return $schedule->fresh();
    }

    protected function validate(array $data, ?WorkSchedule $schedule = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_minutes' => 'required|integer|min:0',
            'late_grace_minutes' => 'required|integer|min:0',
            'status' => 'boolean',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

