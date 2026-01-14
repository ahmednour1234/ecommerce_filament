<?php

namespace App\Services\Finance;

use App\Models\Finance\BranchTransaction;
use Illuminate\Support\Facades\DB;

class BranchTransactionService
{
    public function __construct(
        protected CurrencyConverterService $converter,
    ) {}

    public function generateDocumentNo(): string
    {
        // TX-2026-000001
        $year = now()->format('Y');
        $last = BranchTransaction::withTrashed()
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->value('document_no');

        $seq = 1;
        if ($last && preg_match('/TX-\d{4}-(\d+)/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return 'TX-' . $year . '-' . str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
    }

    public function create(array $data): BranchTransaction
    {
        return DB::transaction(function () use ($data) {
            $data['document_no'] = $data['document_no'] ?? $this->generateDocumentNo();
            $data['created_by']  = $data['created_by'] ?? auth()->id();
            $data['status']      = $data['status'] ?? 'pending';

            $baseCurrencyId = $this->converter->getDefaultCurrencyId();
            $date = $data['transaction_date'] ?? now()->toDateString();

            $conv = $this->converter->convert(
                (float) $data['amount'],
                (int) $data['currency_id'],
                $baseCurrencyId,
                $date
            );

            $data['rate_used'] = $conv['rate'];
            $data['amount_base'] = $conv['converted'];

            return BranchTransaction::create($data);
        });
    }

    public function approve(BranchTransaction $tx, ?string $note = null): BranchTransaction
    {
        return DB::transaction(function () use ($tx, $note) {
            $tx->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_note' => $note,
            ]);
            return $tx;
        });
    }

    public function reject(BranchTransaction $tx, ?string $note = null): BranchTransaction
    {
        return DB::transaction(function () use ($tx, $note) {
            $tx->update([
                'status' => 'rejected',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
                'rejection_note' => $note,
            ]);
            return $tx;
        });
    }
}
