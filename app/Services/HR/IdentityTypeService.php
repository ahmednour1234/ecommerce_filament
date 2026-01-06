<?php

namespace App\Services\HR;

use App\Models\HR\IdentityType;
use Illuminate\Support\Facades\Validator;

class IdentityTypeService
{
    /**
     * Get all identity types
     */
    public function getAll(array $filters = [])
    {
        $query = IdentityType::query();

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get active identity types
     */
    public function getActive()
    {
        return IdentityType::active()->orderBy('name')->get();
    }

    /**
     * Create a new identity type
     */
    public function create(array $data): IdentityType
    {
        $validated = $this->validate($data);
        
        return IdentityType::create($validated);
    }

    /**
     * Update an identity type
     */
    public function update(IdentityType $identityType, array $data): IdentityType
    {
        $validated = $this->validate($data, $identityType);
        
        $identityType->update($validated);
        
        return $identityType->fresh();
    }

    /**
     * Delete an identity type
     */
    public function delete(IdentityType $identityType): bool
    {
        return $identityType->delete();
    }

    /**
     * Validate identity type data
     */
    protected function validate(array $data, ?IdentityType $identityType = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'active' => 'boolean',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

