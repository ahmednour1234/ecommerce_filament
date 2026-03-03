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

        $paymentStatus = 'paid';

        if ($totalCost > 0) {
            if ($remainingTotal <= 0) {
                $paymentStatus = 'paid';
            } elseif ($paidTotal > 0) {
                $paymentStatus = 'partial';
            }
        } else {
            $paymentStatus = $contract->payment_status ?? 'paid';
        }

        return [
            'total_cost' => $totalCost,
            'paid_total' => $paidTotal,
            'remaining_total' => $remainingTotal,
            'payment_status' => $paymentStatus,
        ];
    }

    public function getExpectedDaysBetweenStatuses(?string $fromStatus, string $toStatus): ?int
    {
        $statusOrder = [
            'new' => 1,
            'external_office_approval' => 2,
            'contract_accepted_external_office' => 3,
            'waiting_approval' => 4,
            'contract_accepted_labor_ministry' => 5,
            'sent_to_saudi_embassy' => 6,
            'visa_issued' => 7,
            'waiting_flight_booking' => 8,
        ];

        $expectedDays = [
            '1-2' => 5, // new -> external_office_approval
            '2-3' => 5, // external_office_approval -> contract_accepted_external_office
            '3-4' => 5, // contract_accepted_external_office -> waiting_approval
            '4-5' => 4, // waiting_approval -> contract_accepted_labor_ministry
            '5-6' => 7, // contract_accepted_labor_ministry -> sent_to_saudi_embassy
            '6-7' => 10, // sent_to_saudi_embassy -> visa_issued
            '7-8' => 6, // visa_issued -> waiting_flight_booking
        ];

        if (!$fromStatus || !isset($statusOrder[$fromStatus]) || !isset($statusOrder[$toStatus])) {
            return null;
        }

        $fromOrder = $statusOrder[$fromStatus];
        $toOrder = $statusOrder[$toStatus];

        if ($toOrder <= $fromOrder) {
            return null;
        }

        $key = "{$fromOrder}-{$toOrder}";
        return $expectedDays[$key] ?? null;
    }

    public function logStatusChange(RecruitmentContract $contract, ?string $oldStatus, string $newStatus, ?string $notes = null, ?string $statusDate = null): void
    {
        $expectedDays = $this->getExpectedDaysBetweenStatuses($oldStatus, $newStatus);
        
        $contract->statusLogs()->create([
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'status_date' => $statusDate ?: now()->toDateString(),
            'notes' => $notes,
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);
    }

    public function updateStatus(RecruitmentContract $contract, string $newStatus, ?string $notes = null, ?string $statusDate = null): void
    {
        DB::transaction(function () use ($contract, $newStatus, $notes, $statusDate) {
            $oldStatus = $contract->status;
            $contract->update(['status' => $newStatus]);
            $this->logStatusChange($contract, $oldStatus, $newStatus, $notes, $statusDate);
        });
    }
}
