<?php

namespace App\Services\Recruitment;

use App\Models\Recruitment\RecruitmentContract;
use Illuminate\Support\Facades\DB;

class RecruitmentContractService
{
    public function generateContractNo(): string
    {
        $date = now()->format('Ymd');
        $prefix = "REC-{$date}-";
        
        $last = RecruitmentContract::withTrashed()
            ->where('contract_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('contract_no');
        
        $seq = 1;
        if ($last && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }
        
        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }

    public function computeTotals(RecruitmentContract $contract): array
    {
        $totalCost = $contract->direct_cost 
            + $contract->internal_ticket_cost 
            + $contract->external_cost 
            + $contract->vat_cost 
            + $contract->gov_cost;

        $paidTotal = $contract->receipts()
            ->sum('amount');

        $remainingTotal = max(0, $totalCost - $paidTotal);

        $paymentStatus = 'unpaid';
        if ($remainingTotal <= 0 && $totalCost > 0) {
            $paymentStatus = 'paid';
        } elseif ($paidTotal > 0) {
            $paymentStatus = 'partial';
        }

        return [
            'total_cost' => $totalCost,
            'paid_total' => $paidTotal,
            'remaining_total' => $remainingTotal,
            'payment_status' => $paymentStatus,
        ];
    }

    public function logStatusChange(RecruitmentContract $contract, ?string $oldStatus, string $newStatus, ?string $notes = null): void
    {
        $contract->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }

    public function updateStatus(RecruitmentContract $contract, string $newStatus, ?string $notes = null): void
    {
        DB::transaction(function () use ($contract, $newStatus, $notes) {
            $oldStatus = $contract->status;
            $contract->update(['status' => $newStatus]);
            $this->logStatusChange($contract, $oldStatus, $newStatus, $notes);
        });
    }
}
