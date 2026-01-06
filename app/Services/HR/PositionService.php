<?php

namespace App\Services\HR;

use App\Models\HR\Position;
use App\Models\HR\Department;
use Illuminate\Support\Facades\Validator;

class PositionService
{
    /**
     * Get all positions
     */
    public function getAll(array $filters = [])
    {
        $query = Position::with('department');

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        return $query->orderBy('title')->get();
    }

    /**
     * Get active positions
     */
    public function getActive()
    {
        return Position::with('department')->active()->orderBy('title')->get();
    }

    /**
     * Create a new position
     */
    public function create(array $data): Position
    {
        $validated = $this->validate($data);
        
        return Position::create($validated);
    }

    /**
     * Update a position
     */
    public function update(Position $position, array $data): Position
    {
        $validated = $this->validate($data, $position);
        
        $position->update($validated);
        
        return $position->fresh()->load('department');
    }

    /**
     * Delete a position
     */
    public function delete(Position $position): bool
    {
        return $position->delete();
    }

    /**
     * Validate position data
     */
    protected function validate(array $data, ?Position $position = null): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'department_id' => 'nullable|exists:hr_departments,id',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

