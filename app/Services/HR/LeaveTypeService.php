<?php

namespace App\Services\HR;

use App\Models\HR\LeaveType;
use App\Repositories\HR\LeaveTypeRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LeaveTypeService
{
    protected LeaveTypeRepository $repository;

    public function __construct(LeaveTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all leave types
     */
    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    /**
     * Get paginated leave types
     */
    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    /**
     * Get active leave types
     */
    public function getActive()
    {
        return $this->repository->getActive();
    }

    /**
     * Find leave type by ID
     */
    public function findById(int $id): ?LeaveType
    {
        return $this->repository->findById($id);
    }

    /**
     * Create a new leave type
     */
    public function create(array $data): LeaveType
    {
        $validated = $this->validate($data);
        $validated['created_by'] = Auth::id();
        
        return $this->repository->create($validated);
    }

    /**
     * Update leave type
     */
    public function update(LeaveType $leaveType, array $data): LeaveType
    {
        $validated = $this->validate($data, $leaveType);
        $validated['updated_by'] = Auth::id();
        
        $this->repository->update($leaveType, $validated);
        
        return $leaveType->fresh();
    }

    /**
     * Delete leave type
     */
    public function delete(LeaveType $leaveType): bool
    {
        // Check if there are any leave requests using this type
        if ($leaveType->leaveRequests()->count() > 0) {
            throw new \Exception('Cannot delete leave type with existing leave requests.');
        }
        
        return $this->repository->delete($leaveType);
    }

    /**
     * Validate leave type data
     */
    protected function validate(array $data, ?LeaveType $leaveType = null): array
    {
        $rules = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'allowed_days_per_year' => 'required|integer|min:0|max:365',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

