<?php

namespace App\Services\HR;

use App\Models\HR\BloodType;
use Illuminate\Support\Facades\Validator;

class BloodTypeService
{
    /**
     * Get all blood types
     */
    public function getAll(array $filters = [])
    {
        $query = BloodType::query();

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get active blood types
     */
    public function getActive()
    {
        return BloodType::active()->orderBy('name')->get();
    }

    /**
     * Create a new blood type
     */
    public function create(array $data): BloodType
    {
        $validated = $this->validate($data);
        
        return BloodType::create($validated);
    }

    /**
     * Update a blood type
     */
    public function update(BloodType $bloodType, array $data): BloodType
    {
        $validated = $this->validate($data, $bloodType);
        
        $bloodType->update($validated);
        
        return $bloodType->fresh();
    }

    /**
     * Delete a blood type
     */
    public function delete(BloodType $bloodType): bool
    {
        return $bloodType->delete();
    }

    /**
     * Validate blood type data
     */
    protected function validate(array $data, ?BloodType $bloodType = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'active' => 'boolean',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

