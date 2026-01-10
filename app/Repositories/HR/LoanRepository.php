<?php

namespace App\Repositories\HR;

use App\Models\HR\Loan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class LoanRepository
{
    protected Loan $model;

    public function __construct(Loan $model)
    {
        $this->model = $model;
    }

    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->newQuery()->with(['employee', 'loanType']);

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['loan_type_id'])) {
            $query->where('loan_type_id', $filters['loan_type_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('start_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('id', 'desc')->get();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['employee', 'loanType']);

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['loan_type_id'])) {
            $query->where('loan_type_id', $filters['loan_type_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('start_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('start_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?Loan
    {
        return $this->model->with(['employee', 'loanType', 'installments'])->find($id);
    }

    public function create(array $data): Loan
    {
        return $this->model->create($data);
    }

    public function update(Loan $loan, array $data): bool
    {
        return $loan->update($data);
    }

    public function delete(Loan $loan): bool
    {
        return $loan->delete();
    }
}
