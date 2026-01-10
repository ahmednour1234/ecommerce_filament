<?php

namespace App\Services\HR;

use App\Models\HR\Loan;
use App\Repositories\HR\LoanRepository;
use App\Repositories\HR\LoanInstallmentRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LoanService
{
    protected LoanRepository $repository;
    protected LoanInstallmentRepository $installmentRepository;

    public function __construct(LoanRepository $repository, LoanInstallmentRepository $installmentRepository)
    {
        $this->repository = $repository;
        $this->installmentRepository = $installmentRepository;
    }

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function getPaginated(array $filters = [], int $perPage = 15)
    {
        return $this->repository->getPaginated($filters, $perPage);
    }

    public function findById(int $id): ?Loan
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Loan
    {
        $validated = $this->validate($data);
        
        $loanType = \App\Models\HR\LoanType::find($validated['loan_type_id']);
        if (!$loanType) {
            throw new \Exception('Loan type not found.');
        }

        if ($validated['amount'] > $loanType->max_amount) {
            throw new \Exception("Loan amount exceeds maximum allowed amount of {$loanType->max_amount}.");
        }

        if ($validated['installments_count'] > $loanType->max_installments) {
            throw new \Exception("Installments count exceeds maximum allowed installments of {$loanType->max_installments}.");
        }

        $validated['installment_amount'] = round($validated['amount'] / $validated['installments_count'], 2);
        $validated['status'] = 'active';

        $loan = $this->repository->create($validated);
        $this->generateInstallments($loan);

        return $loan->fresh();
    }

    public function update(Loan $loan, array $data): Loan
    {
        $validated = $this->validate($data, $loan);
        
        $loanType = \App\Models\HR\LoanType::find($validated['loan_type_id']);
        if (!$loanType) {
            throw new \Exception('Loan type not found.');
        }

        if ($validated['amount'] > $loanType->max_amount) {
            throw new \Exception("Loan amount exceeds maximum allowed amount of {$loanType->max_amount}.");
        }

        if ($validated['installments_count'] > $loanType->max_installments) {
            throw new \Exception("Installments count exceeds maximum allowed installments of {$loanType->max_installments}.");
        }

        $validated['installment_amount'] = round($validated['amount'] / $validated['installments_count'], 2);

        $amountChanged = $loan->amount != $validated['amount'];
        $countChanged = $loan->installments_count != $validated['installments_count'];
        $dateChanged = $loan->start_date->format('Y-m-d') != $validated['start_date'];

        if ($amountChanged || $countChanged || $dateChanged) {
            $this->installmentRepository->deletePendingByLoan($loan->id);
            $this->repository->update($loan, $validated);
            $this->generateInstallments($loan->fresh());
        } else {
            $this->repository->update($loan, $validated);
        }

        return $loan->fresh();
    }

    public function delete(Loan $loan): bool
    {
        return $this->repository->delete($loan);
    }

    public function previewSchedule(float $amount, int $count, string $startDate): array
    {
        $installmentAmount = round($amount / $count, 2);
        $start = Carbon::parse($startDate);
        $schedule = [];

        for ($i = 1; $i <= $count; $i++) {
            $dueDate = $start->copy()->addMonthsNoOverflow($i - 1);
            $schedule[] = [
                'installment_no' => $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => $installmentAmount,
            ];
        }

        return $schedule;
    }

    public function generateInstallments(Loan $loan): void
    {
        $installmentAmount = $loan->installment_amount;
        $start = Carbon::parse($loan->start_date);

        for ($i = 1; $i <= $loan->installments_count; $i++) {
            $dueDate = $start->copy()->addMonthsNoOverflow($i - 1);
            
            $this->installmentRepository->create([
                'loan_id' => $loan->id,
                'installment_no' => $i,
                'due_date' => $dueDate->format('Y-m-d'),
                'amount' => $installmentAmount,
                'status' => 'pending',
            ]);
        }
    }
}
