<?php

namespace App\Services\HR;

use App\Models\HR\Department;
use Illuminate\Support\Facades\Validator;

class DepartmentService
{
    /**
     * Get all departments
     */
    public function getAll(array $filters = [])
    {
        $query = Department::query();

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get active departments
     */
    public function getActive()
    {
        return Department::active()->orderBy('name')->get();
    }

    /**
     * Create a new department
     */
    public function create(array $data): Department
    {
        $validated = $this->validate($data);
        
        return Department::create($validated);
    }

    /**
     * Update a department
     */
    public function update(Department $department, array $data): Department
    {
        $validated = $this->validate($data, $department);
        
        $department->update($validated);
        
        return $department->fresh();
    }

    /**
     * Delete a department
     */
    public function delete(Department $department): bool
    {
        return $department->delete();
    }

    /**
     * Validate department data
     */
    protected function validate(array $data, ?Department $department = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'active' => 'boolean',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

