<?php

namespace App\Services\ServiceTransfer;

use App\Models\ServiceTransfer;
use App\Models\ServiceTransferPayment;
use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Services\Finance\BranchTransactionService;
use App\Services\Finance\CurrencyConverterService;
use Illuminate\Support\Facades\DB;

class ServiceTransferPaymentService
{
    public function __construct(
        protected BranchTransactionService $branchTransactionService,
        protected CurrencyConverterService $currencyConverter,
    ) {}

    public function createPayment(array $data): ServiceTransferPayment
    {
        return DB::transaction(function () use ($data) {
            $payment = ServiceTransferPayment::create($data);
            
            $this->createFinanceTransaction($payment);
            $this->updatePaymentStatus($payment->transfer);
            
            return $payment->fresh();
        });
    }

    public function createFinanceTransaction(ServiceTransferPayment $payment): ?BranchTransaction
    {
        $transfer = $payment->transfer;
        if (!$transfer) {
            return null;
        }

        $financeType = FinanceType::where('kind', 'income')
            ->where('code', 'SERVICE_TRANSFER_INCOME')
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
        $branch = $transfer->branch;
        $customer = $transfer->customer;

        $transactionData = [
            'trx_date' => $payment->payment_date->toDateString(),
            'branch_id' => $branch->id ?? null,
            'country_id' => $branch->country_id ?? null,
            'currency_id' => $defaultCurrencyId,
            'finance_type_id' => $financeType->id,
            'amount' => $payment->amount,
            'payment_method' => $payment->paymentMethod->name ?? null,
            'recipient_name' => $customer->name ?? null,
            'reference_no' => $transfer->request_no . '-PAY-' . $payment->payment_no,
            'notes' => "Service Transfer Payment: {$transfer->request_no}",
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ];

        return BranchTransaction::create($transactionData);
    }

    public function updatePaymentStatus(ServiceTransfer $transfer): void
    {
        ServiceTransfer::recalculatePaymentStatus($transfer);
    }
}
