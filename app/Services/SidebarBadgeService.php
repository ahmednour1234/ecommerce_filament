<?php

namespace App\Services;

use App\Models\Recruitment\Laborer;
use App\Models\Rental\RentalContractRequest;
use Illuminate\Support\Facades\Cache;

class SidebarBadgeService
{
    protected int $cacheTtl = 300;

    public function getAvailableWorkersCount(): int
    {
        return Cache::remember('sidebar.badge.available_workers', $this->cacheTtl, function () {
            return Laborer::where('is_available', true)->count();
        });
    }

    public function getHousingRequestsCount(): int
    {
        return Cache::remember('sidebar.badge.housing_requests', $this->cacheTtl, function () {
            return RentalContractRequest::whereIn('status', ['pending', 'under_review'])->count();
        });
    }

    public function getCount(string $badgeKey): ?int
    {
        return match ($badgeKey) {
            'available_workers_count' => $this->getAvailableWorkersCount(),
            'housing_requests_count' => $this->getHousingRequestsCount(),
            default => null,
        };
    }

    public function clearCache(): void
    {
        Cache::forget('sidebar.badge.available_workers');
        Cache::forget('sidebar.badge.housing_requests');
    }
}
