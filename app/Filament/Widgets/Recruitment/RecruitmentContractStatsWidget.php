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

        $newCount = (clone $baseQuery)->where('status', 'new')->count();
        $returnedCount = (clone $baseQuery)->where('status', 'return_during_warranty')->count();
        $warrantyCount = (clone $baseQuery)->inWarranty()->count();
        $externalOfficeApprovalCount = (clone $baseQuery)->where('status', 'external_office_approval')->count();
        $contractAcceptedExternalCount = (clone $baseQuery)->where('status', 'contract_accepted_external_office')->count();
        $waitingApprovalCount = (clone $baseQuery)->where('status', 'waiting_approval')->count();
        $contractAcceptedLaborCount = (clone $baseQuery)->where('status', 'contract_accepted_labor_ministry')->count();
        $sentToEmbassyCount = (clone $baseQuery)->where('status', 'sent_to_saudi_embassy')->count();
        $visaIssuedCount = (clone $baseQuery)->where('status', 'visa_issued')->count();
        $travelPermitAfterVisaIssuedCount = (clone $baseQuery)->where('status', 'travel_permit_after_visa_issued')->count();
        $waitingFlightCount = (clone $baseQuery)->where('status', 'waiting_flight_booking')->count();
        $arrivalScheduledCount = (clone $baseQuery)->where('status', 'arrival_scheduled')->count();
        $receivedCount = (clone $baseQuery)->where('status', 'received')->count();
        $runawayCount = (clone $baseQuery)->where('status', 'runaway')->count();

        $stats[] = Stat::make(
            '📄 ' . (tr('recruitment_contract.stats.new', [], null, 'dashboard') ?: 'عقود جديدة'),
            Number::format($newCount)
        )
            ->description(tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'جديد')
            ->color('primary')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'new']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '🔄 ' . (tr('recruitment_contract.stats.returned', [], null, 'dashboard') ?: 'عقود مسترجعة'),
            Number::format($returnedCount)
        )
            ->description(tr('recruitment_contract.status.return_during_warranty', [], null, 'dashboard') ?: 'رجيع خلال فترة الضمان')
            ->color('warning')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'return_during_warranty']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '🛡️ ' . (tr('recruitment_contract.stats.warranty', [], null, 'dashboard') ?: 'عقود بفترة الضمان'),
            Number::format($warrantyCount)
        )
            ->description(tr('recruitment_contract.stats.warranty', [], null, 'dashboard') ?: 'بفترة الضمان')
            ->color('info')
            ->url($publicUrl)
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '📝 ' . (tr('recruitment_contract.status.external_office_approval', [], null, 'dashboard') ?: 'موافقة المكتب الخارجي'),
            Number::format($externalOfficeApprovalCount)
        )
            ->description(tr('recruitment_contract.status.external_office_approval', [], null, 'dashboard') ?: 'موافقة المكتب الخارجي')
            ->color('info')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'external_office_approval']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '✅ ' . (tr('recruitment_contract.status.contract_accepted_external_office', [], null, 'dashboard') ?: 'قبول العقد من مكتب الخارجي'),
            Number::format($contractAcceptedExternalCount)
        )
            ->description(tr('recruitment_contract.status.contract_accepted_external_office', [], null, 'dashboard') ?: 'قبول العقد من مكتب الخارجي')
            ->color('success')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'contract_accepted_external_office']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '⏳ ' . (tr('recruitment_contract.status.waiting_approval', [], null, 'dashboard') ?: 'انتظار الابروف'),
            Number::format($waitingApprovalCount)
        )
            ->description(tr('recruitment_contract.status.waiting_approval', [], null, 'dashboard') ?: 'انتظار الابروف')
            ->color('warning')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'waiting_approval']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '📋 ' . (tr('recruitment_contract.status.contract_accepted_labor_ministry', [], null, 'dashboard') ?: 'قبول العقد من مكتب العمل الخارجي'),
            Number::format($contractAcceptedLaborCount)
        )
            ->description(tr('recruitment_contract.status.contract_accepted_labor_ministry', [], null, 'dashboard') ?: 'قبول العقد من مكتب العمل الخارجي')
            ->color('success')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'contract_accepted_labor_ministry']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '📤 ' . (tr('recruitment_contract.status.sent_to_saudi_embassy', [], null, 'dashboard') ?: 'إرسال التأشيرة إلى السفارة السعودية'),
            Number::format($sentToEmbassyCount)
        )
            ->description(tr('recruitment_contract.status.sent_to_saudi_embassy', [], null, 'dashboard') ?: 'إرسال التأشيرة إلى السفارة السعودية')
            ->color('info')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'sent_to_saudi_embassy']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '✅ ' . (tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم التفييز'),
            Number::format($visaIssuedCount)
        )
            ->description(tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم التفييز')
            ->color('success')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'visa_issued']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '✈️ ' . (tr('recruitment_contract.status.travel_permit_after_visa_issued', [], null, 'dashboard') ?: 'تصريح سفر بعد تم التفييز'),
            Number::format($travelPermitAfterVisaIssuedCount)
        )
            ->description(tr('recruitment_contract.status.travel_permit_after_visa_issued', [], null, 'dashboard') ?: 'تصريح سفر بعد تم التفييز')
            ->color('success')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'travel_permit_after_visa_issued']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '🎫 ' . (tr('recruitment_contract.status.waiting_flight_booking', [], null, 'dashboard') ?: 'انتظار حجز تذكرة الطيران'),
            Number::format($waitingFlightCount)
        )
            ->description(tr('recruitment_contract.status.waiting_flight_booking', [], null, 'dashboard') ?: 'انتظار حجز تذكرة الطيران')
            ->color('warning')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'waiting_flight_booking']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '📅 ' . (tr('recruitment_contract.status.arrival_scheduled', [], null, 'dashboard') ?: 'معاد الوصول'),
            Number::format($arrivalScheduledCount)
        )
            ->description(tr('recruitment_contract.status.arrival_scheduled', [], null, 'dashboard') ?: 'معاد الوصول')
            ->color('info')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'arrival_scheduled']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '📦 ' . (tr('recruitment_contract.status.received', [], null, 'dashboard') ?: 'تم الاستلام'),
            Number::format($receivedCount)
        )
            ->description(tr('recruitment_contract.status.received', [], null, 'dashboard') ?: 'تم الاستلام')
            ->color('success')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'received']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

        $stats[] = Stat::make(
            '🚨 ' . (tr('recruitment_contract.status.runaway', [], null, 'dashboard') ?: 'هروب'),
            Number::format($runawayCount)
        )
            ->description(tr('recruitment_contract.status.runaway', [], null, 'dashboard') ?: 'هروب')
            ->color('danger')
            ->url($this->buildUrl($publicUrl, array_merge($currentFilters, ['status' => ['value' => 'runaway']])))
            ->extraAttributes(['class' => 'recruitment-stats-card']);

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
