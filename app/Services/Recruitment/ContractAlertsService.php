<?php

namespace App\Services\Recruitment;

use App\Models\Recruitment\RecruitmentContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ContractAlertsService
{
    const CACHE_KEY_25_DAYS = 'recruitment_contracts_alerts_25_days';
    const CACHE_KEY_3_MONTHS = 'recruitment_contracts_alerts_3_months';
    const CACHE_TTL = 3600; // 1 hour

    public function getContractsOver25DaysWithoutProgress(): Collection
    {
        return Cache::remember(self::CACHE_KEY_25_DAYS, self::CACHE_TTL, function () {
            return RecruitmentContract::query()
                ->where('created_at', '<=', now()->subDays(25))
                ->whereNotIn('status', ['new', 'contract_signed', 'visa_issued'])
                ->with(['client', 'branch'])
                ->get()
                ->map(function ($contract) {
                    $contract->alert_type = 'over_25_days';
                    $contract->days_overdue = $contract->created_at->diffInDays(now());
                    return $contract;
                });
        });
    }

    public function getContractsOver3MonthsNotArrived(): Collection
    {
        return Cache::remember(self::CACHE_KEY_3_MONTHS, self::CACHE_TTL, function () {
            return RecruitmentContract::query()
                ->where('created_at', '<=', now()->subMonths(3))
                ->where('status', '!=', 'arrived_in_saudi_arabia')
                ->with(['client', 'branch'])
                ->get()
                ->map(function ($contract) {
                    $contract->alert_type = 'over_3_months';
                    $contract->months_overdue = $contract->created_at->diffInMonths(now());
                    return $contract;
                });
        });
    }

    public function getAllAlerts(): Collection
    {
        $alerts25Days = $this->getContractsOver25DaysWithoutProgress();
        $alerts3Months = $this->getContractsOver3MonthsNotArrived();

        return $alerts25Days->concat($alerts3Months);
    }

    public function getAlertsCount(): int
    {
        return $this->getAllAlerts()->count();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_25_DAYS);
        Cache::forget(self::CACHE_KEY_3_MONTHS);
    }
}
