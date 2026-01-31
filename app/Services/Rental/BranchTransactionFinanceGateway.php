<?php

namespace App\Services\Rental;

use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\Rental\RentalContract;
use App\Services\Finance\BranchTransactionService;
use App\Services\Finance\CurrencyConverterService;
use Illuminate\Support\Facades\DB;

class BranchTransactionFinanceGateway implements FinanceGateway
{
    public function __construct(
        protected BranchTransactionService $transactionService,
        protected CurrencyConverterService $currencyConverter,
    ) {}

    public function postIncome(RentalContract $contract, float $amount, array $meta = []): ?int
    {
        return DB::transaction(function () use ($contract, $amount, $meta) {
            $financeType = FinanceType::where('kind', 'income')
                ->where('code', 'RENTALS')
                ->where('is_active', true)
                ->first();
            
            if (!$financeType) {
                $financeType = FinanceType::where('kind', 'income')
                    ->where('is_active', true)
                    ->first();
            }
            
            if (!$financeType) {
                return null;
            }
            
            $defaultCurrencyId = $this->currencyConverter->getDefaultCurrencyId();
            $branch = $contract->branch;
            $customer = $contract->customer;
            
            $transaction = BranchTransaction::create([
                'trx_date' => now()->toDateString(),
                'branch_id' => $branch->id,
                'country_id' => $contract->country_id,
                'currency_id' => $defaultCurrencyId,
                'finance_type_id' => $financeType->id,
                'amount' => $amount,
                'payment_method' => $meta['payment_method'] ?? null,
                'recipient_name' => $customer->name ?? null,
                'reference_no' => $contract->contract_no,
                'notes' => "Rental contract payment: {$contract->contract_no}" . ($meta['note'] ? " - {$meta['note']}" : ''),
                'created_by' => auth()->id(),
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            return $transaction->id;
        });
    }

    public function postRefund(RentalContract $contract, float $amount, array $meta = []): ?int
    {
        return DB::transaction(function () use ($contract, $amount, $meta) {
            $financeType = FinanceType::where('kind', 'expense')
                ->where(function ($q) {
                    $q->where('code', 'SALES_RETURNS')
                      ->orWhere('code', 'REFUNDS');
                })
                ->where('is_active', true)
                ->first();
            
            if (!$financeType) {
                $financeType = FinanceType::where('kind', 'expense')
                    ->where('is_active', true)
                    ->first();
            }
            
            if (!$financeType) {
                return null;
            }
            
            $defaultCurrencyId = $this->currencyConverter->getDefaultCurrencyId();
            $branch = $contract->branch;
            $customer = $contract->customer;
            
            $transaction = BranchTransaction::create([
                'trx_date' => now()->toDateString(),
                'branch_id' => $branch->id,
                'country_id' => $contract->country_id,
                'currency_id' => $defaultCurrencyId,
                'finance_type_id' => $financeType->id,
                'amount' => -abs($amount),
                'payment_method' => $meta['payment_method'] ?? null,
                'recipient_name' => $customer->name ?? null,
                'reference_no' => $contract->contract_no . '-REFUND',
                'notes' => "Rental contract refund: {$contract->contract_no}" . ($meta['note'] ? " - {$meta['note']}" : ''),
                'created_by' => auth()->id(),
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            return $transaction->id;
        });
    }
}
