<?php

namespace App\Filament\Widgets\Recruitment;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class RecruitmentContractStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $baseQuery = $this->getBaseQuery();
        $baseUrl = RecruitmentContractResource::getUrl('index');
        // Extract path from URL if it's a full URL
        $publicUrl = $this->normalizeUrl($baseUrl);

        $currentFilters = $this->getCurrentFilters();

        $stats = [];

        $newCount = (clone $baseQuery)->new()->count();
        $expiredCount = (clone $baseQuery)->expired()->count();
        $returnedCount = (clone $baseQuery)->returned()->count();
        $warrantyCount = (clone $baseQuery)->inWarranty()->count();
        $rejectedCount = (clone $baseQuery)->rejected()->count();
        $signedCount = (clone $baseQuery)->signed()->count();
        $visaIssuedCount = (clone $baseQuery)->visaIssued()->count();
        $arrivalTicketCount = (clone $baseQuery)->arrivalTicketIssued()->count();

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.new', [], null, 'dashboard') ?: 'عقود جديدة',
            Number::format($newCount)
        )
            ->description(tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'جديد')
            ->color('primary')
            ->icon('heroicon-o-document-text')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'new']])));

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.expired', [], null, 'dashboard') ?: 'العقود المنتهية',
            Number::format($expiredCount)
        )
            ->description(tr('recruitment_contract.status.closed', [], null, 'dashboard') ?: 'مغلق')
            ->color('gray')
            ->icon('heroicon-o-clock')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'closed']])));

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.returned', [], null, 'dashboard') ?: 'عقود مسترجعة',
            Number::format($returnedCount)
        )
            ->description(tr('recruitment_contract.status.returned', [], null, 'dashboard') ?: 'مرتجع')
            ->color('warning')
            ->icon('heroicon-o-arrow-path')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'returned']])));

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.warranty', [], null, 'dashboard') ?: 'عقود بفترة الضمان',
            Number::format($warrantyCount)
        )
            ->description(tr('recruitment_contract.stats.warranty', [], null, 'dashboard') ?: 'بفترة الضمان')
            ->color('info')
            ->icon('heroicon-o-shield-check')
            ->url($publicUrl);

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.rejected', [], null, 'dashboard') ?: 'عقود مرفوضة',
            Number::format($rejectedCount)
        )
            ->description(tr('recruitment_contract.status.rejected', [], null, 'dashboard') ?: 'مرفوض')
            ->color('danger')
            ->icon('heroicon-o-x-circle')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'rejected']])));

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.signed', [], null, 'dashboard') ?: 'عقود تم توقيع العقد',
            Number::format($signedCount)
        )
            ->description(tr('recruitment_contract.status.contract_signed', [], null, 'dashboard') ?: 'تم توقيع العقد')
            ->color('success')
            ->icon('heroicon-o-check-circle')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'contract_signed']])));

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.visa_issued', [], null, 'dashboard') ?: 'عقود تم إصدار تأشيراتها',
            Number::format($visaIssuedCount)
        )
            ->description(tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم إصدار التأشيرة')
            ->color('success')
            ->icon('heroicon-o-document-check')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'visa_issued']])));

        $stats[] = Stat::make(
            tr('recruitment_contract.stats.arrival_ticket_issued', [], null, 'dashboard') ?: 'عقود تم إصدار تذاكر الوصول',
            Number::format($arrivalTicketCount)
        )
            ->description(tr('recruitment_contract.status.ticket_booked', [], null, 'dashboard') ?: 'تم حجز التذكرة')
            ->color('success')
            ->icon('heroicon-o-ticket')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'ticket_booked']])));

        return $stats;
    }

    protected function getBaseQuery()
    {
        $query = RecruitmentContract::query();

        $parentPage = $this->getParentPage();
        if ($parentPage) {
            if (method_exists($parentPage, 'getFilteredTableQuery')) {
                try {
                    $filteredQuery = $parentPage->getFilteredTableQuery();
                    if ($filteredQuery) {
                        return clone $filteredQuery;
                    }
                } catch (\Exception $e) {
                }
            }
            if (method_exists($parentPage, 'getTable')) {
                try {
                    $table = $parentPage->getTable();
                    if ($table && method_exists($table, 'getQuery')) {
                        $tableQuery = $table->getQuery();
                        if ($tableQuery) {
                            return clone $tableQuery;
                        }
                    }
                } catch (\Exception $e) {
                }
            }
        }

        $filters = $this->getCurrentFilters();

        if (isset($filters['branch_id']['value'])) {
            $query->where('branch_id', $filters['branch_id']['value']);
        }

        if (isset($filters['status']['value'])) {
            $query->where('status', $filters['status']['value']);
        }

        if (isset($filters['payment_status']['value'])) {
            $query->where('payment_status', $filters['payment_status']['value']);
        }

        if (isset($filters['created_at']['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_at']['created_from']);
        }

        if (isset($filters['created_at']['created_until'])) {
            $query->whereDate('created_at', '<=', $filters['created_at']['created_until']);
        }

        $search = request()->get('tableSearch');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('contract_no', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name_ar', 'like', "%{$search}%")
                            ->orWhere('name_en', 'like', "%{$search}%");
                    });
            });
        }

        return $query;
    }

    protected function getParentPage()
    {
        if (property_exists($this, 'livewire') && $this->livewire) {
            return $this->livewire;
        }
        return null;
    }

    protected function getCurrentFilters(): array
    {
        $filters = [];

        $tableFilters = request()->get('tableFilters', []);

        if (isset($tableFilters['branch_id'])) {
            $filters['branch_id'] = $tableFilters['branch_id'];
        }

        if (isset($tableFilters['status'])) {
            $filters['status'] = $tableFilters['status'];
        }

        if (isset($tableFilters['payment_status'])) {
            $filters['payment_status'] = $tableFilters['payment_status'];
        }

        if (isset($tableFilters['created_at'])) {
            $filters['created_at'] = $tableFilters['created_at'];
        }

        return $filters;
    }

    protected function normalizeUrl(string $url): string
    {
        // If it's already a full URL, extract just the path
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            $parsed = parse_url($url);
            $path = $parsed['path'] ?? '/';
            // Remove /public if it's already in the path
            if (str_starts_with($path, '/public')) {
                return $path;
            }
            return '/public' . $path;
        }
        
        // If it's already a relative path starting with /public, return as is
        if (str_starts_with($url, '/public')) {
            return $url;
        }
        
        // Otherwise, prepend /public
        return '/public' . $url;
    }

    protected function buildUrl(string $baseUrl, array $filters): string
    {
        $params = [];
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $params["tableFilters[{$key}][{$subKey}]"] = $subValue;
                }
            } else {
                $params["tableFilters[{$key}][value]"] = $value;
            }
        }

        $search = request()->get('tableSearch');
        if ($search) {
            $params['tableSearch'] = $search;
        }

        $queryString = http_build_query($params);
        return $baseUrl . ($queryString ? '?' . $queryString : '');
    }
}
