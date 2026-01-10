<?php

namespace App\Services\HR;

use App\Models\HR\Loan;
use App\Repositories\HR\LoanRepository;
use App\Repositories\HR\LoanInstallmentRepository;
use App\Services\Accounting\CurrencyConversionService;
use App\Services\MainCore\CurrencyService;
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

        $this->calculateCurrencyAndBaseAmount($validated);

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

        $this->calculateCurrencyAndBaseAmount($validated, $loan);

        $amountChanged = $loan->amount != $validated['amount'];
        $countChanged = $loan->installments_count != $validated['installments_count'];
        $dateChanged = $loan->start_date->format('Y-m-d') != $validated['start_date'];
        $currencyChanged = ($loan->currency_id ?? null) != ($validated['currency_id'] ?? null);

        if ($amountChanged || $countChanged || $dateChanged || $currencyChanged) {
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

    protected function calculateCurrencyAndBaseAmount(array &$data, ?Loan $loan = null): void
    {
        $currencyService = app(CurrencyService::class);
        $defaultCurrency = $currencyService->defaultCurrency();

        // If no currency set, use default currency
        if (empty($data['currency_id'])) {
            if ($defaultCurrency) {
                $data['currency_id'] = $defaultCurrency->id;
                $data['exchange_rate'] = 1.0;
            } else {
                $data['exchange_rate'] = 1.0;
            }
        } else {
            // If currency is default currency, set rate to 1
            if ($defaultCurrency && $data['currency_id'] == $defaultCurrency->id) {
                $data['exchange_rate'] = 1.0;
            } elseif (empty($data['exchange_rate']) || ($data['exchange_rate'] ?? 0) == 0) {
                // If exchange rate not set, try to fetch it based on start_date
                try {
                    $conversionService = app(CurrencyConversionService::class);
                    $startDate = $data['start_date'] ?? ($loan?->start_date ?? now());
                    if (is_string($startDate)) {
                        $startDate = Carbon::parse($startDate);
                    } elseif (!$startDate instanceof \DateTime) {
                        $startDate = now();
                    }
                    $data['exchange_rate'] = $conversionService->getExchangeRate((int) $data['currency_id'], $startDate);
                } catch (\Exception $e) {
                    $data['exchange_rate'] = 1.0;
                }
            }
        }

        // Ensure exchange_rate is set
        if (empty($data['exchange_rate']) || $data['exchange_rate'] == 0) {
            $data['exchange_rate'] = 1.0;
        }

        // Calculate base amount
        if (isset($data['amount']) && $data['amount']) {
            $data['base_amount'] = round((float) $data['amount'] * (float) $data['exchange_rate'], 2);
        } else {
            $data['base_amount'] = 0;
        }
    }

    protected function validate(array $data, ?Loan $loan = null): array
    {
        $rules = [
            'employee_id' => ['required', 'exists:hr_employees,id'],
            'loan_type_id' => ['required', 'exists:hr_loan_types,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency_id' => ['nullable', 'exists:currencies,id'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0'],
            'base_amount' => ['nullable', 'numeric', 'min:0'],
            'installments_count' => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'purpose' => ['nullable', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ];

        $validator = Validator::make($data, $rules);
        return $validator->validate();
    }
}
