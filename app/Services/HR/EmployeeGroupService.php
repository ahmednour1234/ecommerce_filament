<?php

namespace App\Services\HR;

use App\Models\HR\EmployeeGroup;
use App\Models\HR\Employee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EmployeeGroupService
{
    public function getAll(array $filters = [])
    {
        $query = EmployeeGroup::query();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('name')->get();
    }

    public function getActive()
    {
        return EmployeeGroup::active()->orderBy('name')->get();
    }

    public function create(array $data): EmployeeGroup
    {
        $validated = $this->validate($data);
        
        return EmployeeGroup::create($validated);
    }

    public function update(EmployeeGroup $group, array $data): EmployeeGroup
    {
        $validated = $this->validate($data, $group);
        
        $group->update($validated);
        
        return $group->fresh();
    }

    public function delete(EmployeeGroup $group): bool
    {
        return $group->delete();
    }

    public function toggleStatus(EmployeeGroup $group): EmployeeGroup
    {
        $group->update(['status' => !$group->status]);
        return $group->fresh();
    }

    public function syncMembers(EmployeeGroup $group, array $employeeIds): void
    {
        $group->employees()->sync($employeeIds);
    }

    public function addMember(EmployeeGroup $group, int $employeeId): void
    {
        $group->employees()->syncWithoutDetaching([$employeeId]);
    }

    public function removeMember(EmployeeGroup $group, int $employeeId): void
    {
        $group->employees()->detach($employeeId);
    }

    protected function validate(array $data, ?EmployeeGroup $group = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'boolean',
            'default_schedule_id' => 'nullable|exists:hr_work_schedules,id',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

