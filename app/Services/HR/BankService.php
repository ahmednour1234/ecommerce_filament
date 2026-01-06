<?php

namespace App\Services\HR;

use App\Models\HR\Bank;
use Illuminate\Support\Facades\Validator;

class BankService
{
    /**
     * Get all banks
     */
    public function getAll(array $filters = [])
    {
        $query = Bank::query();

        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get active banks
     */
    public function getActive()
    {
        return Bank::active()->orderBy('name')->get();
    }

    /**
     * Create a new bank
     */
    public function create(array $data): Bank
    {
        $validated = $this->validate($data);
        
        return Bank::create($validated);
    }

    /**
     * Update a bank
     */
    public function update(Bank $bank, array $data): Bank
    {
        $validated = $this->validate($data, $bank);
        
        $bank->update($validated);
        
        return $bank->fresh();
    }

    /**
     * Delete a bank
     */
    public function delete(Bank $bank): bool
    {
        return $bank->delete();
    }

    /**
     * Validate bank data
     */
    protected function validate(array $data, ?Bank $bank = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'iban_prefix' => 'nullable|string|max:10',
            'active' => 'boolean',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

