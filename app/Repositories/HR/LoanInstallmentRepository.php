<?php

namespace App\Repositories\HR;

use App\Models\HR\LoanInstallment;
use Illuminate\Database\Eloquent\Collection;

class LoanInstallmentRepository
{
    protected LoanInstallment $model;

    public function __construct(LoanInstallment $model)
    {
        $this->model = $model;
    }

    public function getByLoan(int $loanId): Collection
    {
        return $this->model->where('loan_id', $loanId)->orderBy('installment_no')->get();
    }

    public function findById(int $id): ?LoanInstallment
    {
        return $this->model->find($id);
    }

    public function create(array $data): LoanInstallment
    {
        return $this->model->create($data);
    }

    public function update(LoanInstallment $installment, array $data): bool
    {
        return $installment->update($data);
    }

    public function delete(LoanInstallment $installment): bool
    {
        return $installment->delete();
    }

    public function deletePendingByLoan(int $loanId): int
    {
        return $this->model->where('loan_id', $loanId)->where('status', 'pending')->delete();
    }
}
