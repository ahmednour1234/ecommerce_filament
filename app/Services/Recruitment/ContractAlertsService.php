<?php

namespace App\Services\Recruitment;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\Recruitment\RecruitmentContractStatusLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ContractAlertsService
{
    const CACHE_KEY_STATUS_ALERTS = 'recruitment_contracts_status_alerts';
    const CACHE_KEY_SECTION_ALERTS = 'recruitment_contracts_section_alerts';
    const CACHE_TTL = 3600;
    const DAYS_STUCK_AT_SECTION = 2;

    private function getExpectedDaysBetweenStatuses(?string $fromStatus, string $toStatus): ?int
    {
        $expectedDays = [
            'new-external_office_approval' => 5,
            'external_office_approval-contract_accepted_external_office' => 5,
            'contract_accepted_external_office-waiting_approval' => 5,
            'waiting_approval-contract_accepted_labor_ministry' => 4,
            'contract_accepted_labor_ministry-sent_to_saudi_embassy' => 7,
            'sent_to_saudi_embassy-visa_cancelled' => null,
            'sent_to_saudi_embassy-visa_issued' => 10,
            'visa_cancelled-visa_issued' => 10,
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
                    'visa_cancelled',
                    'visa_issued',
                    'waiting_flight_booking'
                ])
                ->where('current_section', RecruitmentContract::SECTION_COORDINATION)
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

    public function getAlertsStuckAtSection(string $section): Collection
    {
        $cacheKey = self::CACHE_KEY_SECTION_ALERTS . '_' . $section;
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($section) {
            $cutoff = now()->subDays(self::DAYS_STUCK_AT_SECTION);
            return RecruitmentContract::query()
                ->where('current_section', $section)
                ->where(function ($q) use ($cutoff) {
                    $q->where('updated_at', '<=', $cutoff)
                        ->orWhere('created_at', '<=', $cutoff);
                })
                ->with(['client', 'branch'])
                ->get()
                ->map(function ($contract) {
                    $contract->alert_type = 'stuck_at_section';
                    $contract->expected_days = self::DAYS_STUCK_AT_SECTION;
                    $contract->days_overdue = (int) \Carbon\Carbon::parse($contract->updated_at)->diffInDays(now());
                    $contract->last_status_change = \Carbon\Carbon::parse($contract->updated_at);
                    return $contract;
                });
        });
    }

    public function getAllAlerts(): Collection
    {
        $section = RecruitmentContractResource::getUserSection();
        if ($section === RecruitmentContract::SECTION_CUSTOMER_SERVICE) {
            return $this->getAlertsStuckAtSection(RecruitmentContract::SECTION_CUSTOMER_SERVICE);
        }
        if ($section === RecruitmentContract::SECTION_ACCOUNTS) {
            return $this->getAlertsStuckAtSection(RecruitmentContract::SECTION_ACCOUNTS);
        }
        if ($section === RecruitmentContract::SECTION_COORDINATION || $section === null) {
            return $this->getContractsExceedingExpectedTime();
        }
        return collect();
    }

    public function getAlertsCount(): int
    {
        return $this->getAllAlerts()->count();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_STATUS_ALERTS);
        Cache::forget(self::CACHE_KEY_SECTION_ALERTS . '_' . RecruitmentContract::SECTION_CUSTOMER_SERVICE);
        Cache::forget(self::CACHE_KEY_SECTION_ALERTS . '_' . RecruitmentContract::SECTION_ACCOUNTS);
    }
}
