<?php

namespace App\Repositories\HR;

use App\Models\HR\LoanType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LoanTypeRepository
{
    protected LoanType $model;

    public function __construct(LoanType $model)
    {
        $this->model = $model;
    }

    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereJsonContains('name_json->ar', $search)
                    ->orWhereJsonContains('name_json->en', $search);
            });
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->whereJsonContains('name_json->ar', $search)
                    ->orWhereJsonContains('name_json->en', $search);
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function getActive(): Collection
    {
        return $this->model->active()->orderBy('id', 'desc')->get();
    }

    public function findById(int $id): ?LoanType
    {
        return $this->model->find($id);
    }

    public function create(array $data): LoanType
    {
        return $this->model->create($data);
    }

    public function update(LoanType $loanType, array $data): bool
    {
        return $loanType->update($data);
    }

    public function delete(LoanType $loanType): bool
    {
        return $loanType->delete();
    }
}
