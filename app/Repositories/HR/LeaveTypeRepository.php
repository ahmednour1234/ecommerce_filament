<?php

namespace App\Repositories\HR;

use App\Models\HR\LeaveType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LeaveTypeRepository
{
    protected LeaveType $model;

    public function __construct(LeaveType $model)
    {
        $this->model = $model;
    }

    /**
     * Get all leave types with optional filters
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name_en')->get();
    }

    /**
     * Get paginated leave types
     */
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name_en')->paginate($perPage);
    }

    /**
     * Get active leave types
     */
    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('name_en')->get();
    }

    /**
     * Find leave type by ID
     */
    public function findById(int $id): ?LeaveType
    {
        return $this->model->find($id);
    }

    /**
     * Create a new leave type
     */
    public function create(array $data): LeaveType
    {
        return $this->model->create($data);
    }

    /**
     * Update leave type
     */
    public function update(LeaveType $leaveType, array $data): bool
    {
        return $leaveType->update($data);
    }

    /**
     * Delete leave type
     */
    public function delete(LeaveType $leaveType): bool
    {
        return $leaveType->delete();
    }
}

