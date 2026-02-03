<?php

namespace App\Services\Recruitment;

use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\Recruitment\RecruitmentContractFinanceLink;
use App\Services\Finance\CurrencyConverterService;
use Illuminate\Support\Facades\DB;

class RecruitmentContractFinanceGateway
{
    public function __construct(
        protected CurrencyConverterService $currencyConverter,
    ) {}

    public function postReceipt(RecruitmentContract $contract, float $amount, array $meta = []): ?int
    {
        return DB::transaction(function () use ($contract, $amount, $meta) {
            $financeType = FinanceType::where('kind', 'income')
                ->where('code', 'RECRUITMENT_RECEIPTS')
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
            $client = $contract->client;
            
            $transaction = BranchTransaction::create([
                'trx_date' => now()->toDateString(),
                'branch_id' => $branch->id,
                'country_id' => $contract->arrival_country_id,
                'currency_id' => $defaultCurrencyId,
                'finance_type_id' => $financeType->id,
                'amount' => $amount,
                'payment_method' => $meta['payment_method'] ?? null,
                'recipient_name' => $client->name_ar ?? $client->name_en ?? null,
                'reference_no' => $contract->contract_no,
                'notes' => "Recruitment contract receipt: {$contract->contract_no}" . ($meta['note'] ? " - {$meta['note']}" : ''),
                'created_by' => auth()->id(),
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            RecruitmentContractFinanceLink::create([
                'recruitment_contract_id' => $contract->id,
                'finance_transaction_id' => $transaction->id,
                'type' => 'receipt',
                'amount' => $amount,
            ]);
            
            return $transaction->id;
        });
    }

    public function postExpense(RecruitmentContract $contract, float $amount, array $meta = []): ?int
    {
        return DB::transaction(function () use ($contract, $amount, $meta) {
            $financeType = FinanceType::where('kind', 'expense')
                ->where('code', 'RECRUITMENT_EXPENSES')
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
            $client = $contract->client;
            
            $transaction = BranchTransaction::create([
                'trx_date' => now()->toDateString(),
                'branch_id' => $branch->id,
                'country_id' => $contract->arrival_country_id,
                'currency_id' => $defaultCurrencyId,
                'finance_type_id' => $financeType->id,
                'amount' => abs($amount),
                'payment_method' => $meta['payment_method'] ?? null,
                'recipient_name' => $meta['recipient_name'] ?? ($client->name_ar ?? $client->name_en ?? null),
                'reference_no' => $contract->contract_no . '-EXP',
                'notes' => "Recruitment contract expense: {$contract->contract_no}" . ($meta['note'] ? " - {$meta['note']}" : ''),
                'created_by' => auth()->id(),
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            RecruitmentContractFinanceLink::create([
                'recruitment_contract_id' => $contract->id,
                'finance_transaction_id' => $transaction->id,
                'type' => 'expense',
                'amount' => abs($amount),
            ]);
            
            return $transaction->id;
        });
    }
}
