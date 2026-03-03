<?php

namespace App\Services\Recruitment;

use App\Models\Recruitment\RecruitmentContract;
use App\Models\Recruitment\RecruitmentContractStatusLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ContractAlertsService
{
    const CACHE_KEY_STATUS_ALERTS = 'recruitment_contracts_status_alerts';
    const CACHE_TTL = 3600; // 1 hour

    private function getExpectedDaysBetweenStatuses(?string $fromStatus, string $toStatus): ?int
    {
        $expectedDays = [
            'new-external_office_approval' => 5,
            'external_office_approval-contract_accepted_external_office' => 5,
            'contract_accepted_external_office-waiting_approval' => 5,
            'waiting_approval-contract_accepted_labor_ministry' => 4,
            'contract_accepted_labor_ministry-sent_to_saudi_embassy' => 7,
            'sent_to_saudi_embassy-visa_issued' => 10,
            'visa_issued-waiting_flight_booking' => 6,
        ];

        if (!$fromStatus) {
            return null;
        }

        $key = "{$fromStatus}-{$toStatus}";
        return $expectedDays[$key] ?? null;
    }

    public function getContractsExceedingExpectedTime(): Collection
    {
        return Cache::remember(self::CACHE_KEY_STATUS_ALERTS, self::CACHE_TTL, function () {
            $contracts = RecruitmentContract::query()
                ->whereIn('status', [
                    'new',
                    'external_office_approval',
                    'contract_accepted_external_office',
                    'waiting_approval',
                    'contract_accepted_labor_ministry',
                    'sent_to_saudi_embassy',
                    'visa_issued',
                    'waiting_flight_booking'
                ])
                ->with(['client', 'branch', 'statusLogs' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->get();

            $alerts = collect();

            foreach ($contracts as $contract) {
                $lastLog = $contract->statusLogs->where('new_status', $contract->status)->first();
                
                if (!$lastLog) {
                    $lastLog = $contract->statusLogs->first();
                }

                if (!$lastLog) {
                    continue;
                }

                $currentStatus = $contract->status;
                $lastStatusChangeDate = $lastLog->created_at;
                $expectedDays = $this->getExpectedDaysBetweenStatuses($lastLog->old_status, $currentStatus);

                if ($expectedDays) {
                    $daysSinceLastChange = $lastStatusChangeDate->diffInDays(now());
                    
                    if ($daysSinceLastChange > $expectedDays) {
                        $contract->alert_type = 'status_exceeded';
                        $contract->expected_days = $expectedDays;
                        $contract->days_overdue = $daysSinceLastChange - $expectedDays;
                        $contract->current_status = $currentStatus;
                        $contract->last_status_change = $lastStatusChangeDate;
                        $alerts->push($contract);
                    }
                }
            }

            return $alerts;
        });
    }

    public function getAllAlerts(): Collection
    {
        return $this->getContractsExceedingExpectedTime();
    }

    public function getAlertsCount(): int
    {
        return $this->getAllAlerts()->count();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_STATUS_ALERTS);
    }
}
