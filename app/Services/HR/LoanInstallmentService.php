<?php

namespace App\Services\HR;

use App\Models\HR\LoanInstallment;
use App\Repositories\HR\LoanInstallmentRepository;
use App\Repositories\HR\LoanRepository;

class LoanInstallmentService
{
    protected LoanInstallmentRepository $repository;
    protected LoanRepository $loanRepository;

    public function __construct(LoanInstallmentRepository $repository, LoanRepository $loanRepository)
    {
        $this->repository = $repository;
        $this->loanRepository = $loanRepository;
    }

    public function markAsPaid(LoanInstallment $installment): LoanInstallment
    {
        $this->repository->update($installment, [
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $loan = $installment->loan;
        $pendingCount = $loan->installments()->where('status', 'pending')->count();

        if ($pendingCount === 0) {
            $this->loanRepository->update($loan, ['status' => 'closed']);
        }

        return $installment->fresh();
    }

    public function getByLoan(int $loanId)
    {
        return $this->repository->getByLoan($loanId);
    }

    public function findById(int $id): ?LoanInstallment
    {
        return $this->repository->findById($id);
    }
}
