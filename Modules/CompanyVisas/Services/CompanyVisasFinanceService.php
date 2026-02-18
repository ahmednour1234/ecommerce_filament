<?php

namespace Modules\CompanyVisas\Services;

use App\Models\Accounting\Account;
use App\Models\Accounting\Journal;
use App\Models\Accounting\JournalEntry;
use App\Models\MainCore\Branch;
use App\Services\Accounting\JournalEntryService;
use Illuminate\Support\Facades\DB;
use Modules\CompanyVisas\Entities\CompanyVisaContract;
use Modules\CompanyVisas\Entities\CompanyVisaContractCost;
use Modules\CompanyVisas\Entities\CompanyVisaContractExpense;

class CompanyVisasFinanceService
{
    protected JournalEntryService $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }

    public function recordContractCost(CompanyVisaContract $contract, CompanyVisaContractCost $cost): ?int
    {
        try {
            return DB::transaction(function () use ($contract, $cost) {
                $journal = Journal::where('type', 'general')->first()
                    ?? Journal::where('type', 'cash')->first();

                if (!$journal) {
                    throw new \Exception('No suitable journal found. Please create a general or cash journal first.');
                }

                $entryNumber = $this->generateEntryNumber($journal);

                $buyerAccount = $this->getBuyerAccount();
                $agentAccount = $this->getAgentAccount($contract->agent_id);

                if (!$buyerAccount || !$agentAccount) {
                    throw new \Exception('Required accounts not found. Please configure buyer and agent accounts.');
                }

                $branch = Branch::first() ?? null;

                $entry = $this->journalEntryService->create([
                    'journal_id' => $journal->id,
                    'entry_number' => $entryNumber,
                    'entry_date' => $cost->due_date,
                    'reference' => $contract->contract_no,
                    'description' => "تكلفة استقدام - عقد رقم {$contract->contract_no}",
                    'branch_id' => $branch?->id,
                    'user_id' => auth()->id(),
                    'lines' => [
                        [
                            'account_id' => $buyerAccount->id,
                            'debit' => $cost->total_cost,
                            'credit' => 0,
                            'description' => "تكلفة استقدام - عقد رقم {$contract->contract_no}",
                            'branch_id' => $branch?->id,
                        ],
                        [
                            'account_id' => $agentAccount->id,
                            'debit' => 0,
                            'credit' => $cost->total_cost,
                            'description' => "تكلفة استقدام - عقد رقم {$contract->contract_no}",
                            'branch_id' => $branch?->id,
                        ],
                    ],
                ]);

                $cost->finance_entry_id = $entry->id;
                $cost->save();

                return $entry->id;
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create finance entry for contract cost: ' . $e->getMessage());
            return null;
        }
    }

    public function recordExpense(CompanyVisaContract $contract, CompanyVisaContractExpense $expense): ?int
    {
        try {
            return DB::transaction(function () use ($contract, $expense) {
                $journal = Journal::where('type', 'general')->first()
                    ?? Journal::where('type', 'cash')->first();

                if (!$journal) {
                    throw new \Exception('No suitable journal found. Please create a general or cash journal first.');
                }

                $entryNumber = $this->generateEntryNumber($journal);

                $expenseAccount = Account::find($expense->expense_account_id);
                $cashAccount = $this->getPaymentMethodAccount($expense->payment_method_id);

                if (!$expenseAccount || !$cashAccount) {
                    throw new \Exception('Required accounts not found. Please configure expense and cash accounts.');
                }

                $amount = $expense->amount;
                if ($expense->includes_vat) {
                    $vatAmount = $amount * 0.15;
                    $amount += $vatAmount;
                }

                $branch = Branch::first() ?? null;

                $entry = $this->journalEntryService->create([
                    'journal_id' => $journal->id,
                    'entry_number' => $entryNumber,
                    'entry_date' => $expense->expense_date,
                    'reference' => $expense->invoice_no ?? $contract->contract_no,
                    'description' => $expense->description ?? "مصروف عقد رقم {$contract->contract_no}",
                    'branch_id' => $branch?->id,
                    'user_id' => auth()->id(),
                    'lines' => [
                        [
                            'account_id' => $expenseAccount->id,
                            'debit' => $amount,
                            'credit' => 0,
                            'description' => $expense->description ?? "مصروف عقد رقم {$contract->contract_no}",
                            'branch_id' => $branch?->id,
                        ],
                        [
                            'account_id' => $cashAccount->id,
                            'debit' => 0,
                            'credit' => $amount,
                            'description' => $expense->description ?? "مصروف عقد رقم {$contract->contract_no}",
                            'branch_id' => $branch?->id,
                        ],
                    ],
                ]);

                $expense->finance_entry_id = $entry->id;
                $expense->save();

                return $entry->id;
            });
        } catch (\Exception $e) {
            \Log::error('Failed to create finance entry for expense: ' . $e->getMessage());
            return null;
        }
    }

    protected function getBuyerAccount(): ?Account
    {
        $accountCode = config('company_visas.buyer_account_code', '1000');
        return Account::where('code', $accountCode)
            ->orWhere('name', 'like', '%مشترى%')
            ->orWhere('name', 'like', '%buyer%')
            ->first();
    }

    protected function getAgentAccount(?int $agentId): ?Account
    {
        if (!$agentId) {
            return null;
        }

        $accountCode = config("company_visas.agent_account_code_{$agentId}");
        if ($accountCode) {
            return Account::where('code', $accountCode)->first();
        }

        return Account::where('name', 'like', '%وكيل%')
            ->orWhere('name', 'like', '%agent%')
            ->first();
    }

    protected function getPaymentMethodAccount(?int $paymentMethodId): ?Account
    {
        if ($paymentMethodId) {
            $accountCode = config("company_visas.payment_method_account_code_{$paymentMethodId}");
            if ($accountCode) {
                return Account::where('code', $accountCode)->first();
            }
        }

        return Account::where('code', '1000')
            ->orWhere('name', 'like', '%نقد%')
            ->orWhere('name', 'like', '%cash%')
            ->first();
    }

    protected function generateEntryNumber(Journal $journal): string
    {
        $prefix = strtoupper(substr($journal->code, 0, 3));
        $lastEntry = JournalEntry::where('journal_id', $journal->id)->latest('id')->first();
        $number = $lastEntry ? ((int) substr($lastEntry->entry_number, -6)) + 1 : 1;
        return $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
