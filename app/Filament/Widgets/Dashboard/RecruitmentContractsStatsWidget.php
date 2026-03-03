<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class RecruitmentContractsStatsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = null;

    protected $listeners = ['filters-updated' => '$refresh'];

    public function getHeading(): string
    {
        return 'إحصائيات عقود الاستقدام';
    }

    protected function getFilters(): array
    {
        if (session()->has('dashboard_filters')) {
            return session()->get('dashboard_filters');
        }
        return \App\Helpers\DashboardFilterHelper::parseFiltersFromRequest();
    }

    protected function getStats(): array
    {
        $filters = $this->getFilters();
        $from = $filters['date_from'] ?? now()->startOfMonth();
        $to = $filters['date_to'] ?? now()->endOfMonth();
        $branchId = $filters['branch_id'] ?? null;

        if (is_string($from)) {
            $from = Carbon::parse($from)->startOfDay();
        }
        if (is_string($to)) {
            $to = Carbon::parse($to)->endOfDay();
        }

        $cacheKey = "dashboard_recruitment_contracts_stats_{$branchId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $baseFilters = [
                'created_at' => [
                    'created_from' => $from->format('Y-m-d'),
                    'created_until' => $to->format('Y-m-d'),
                ],
            ];
            
            if ($branchId) {
                $baseFilters['branch_id'] = ['value' => $branchId];
            }

            $baseUrl = RecruitmentContractResource::getUrl('index');
            // Extract path from URL if it's a full URL
            $publicUrl = $this->normalizeUrl($baseUrl);
            $query = RecruitmentContract::query()
                ->whereBetween('created_at', [$from, $to]);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalContracts = $query->count();
            $newContracts = (clone $query)->where('status', 'new')->count();
            $receivedContracts = (clone $query)->where('status', 'received')->count();
            $visaIssued = (clone $query)->where('status', 'visa_issued')->count();
            $returnedContracts = (clone $query)->where('status', 'return_during_warranty')->count();
            $runawayContracts = (clone $query)->where('status', 'runaway')->count();

            $totalCost = (clone $query)->sum('total_cost');
            $paidTotal = (clone $query)->sum('paid_total');
            $remainingTotal = (clone $query)->sum('remaining_total');

            $stats = [];

            if ($totalContracts === 0) {
                return [
                    Stat::make(
                        'لا توجد بيانات',
                        'لا توجد عقود في الفترة المحددة'
                    )
                        ->description('')
                        ->color('gray')
                        ->icon('heroicon-o-information-circle'),
                ];
            }

            $stats[] = Stat::make(
                'إجمالي العقود',
                Number::format($totalContracts)
            )
                ->description('في الفترة المحددة')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->icon('heroicon-o-document-text')
                ->url($this->buildUrl($publicUrl, $baseFilters));

            if ($newContracts > 0) {
                $stats[] = Stat::make(
                    'عقود جديدة',
                    Number::format($newContracts)
                )
                    ->description('جديد')
                    ->descriptionIcon('heroicon-o-plus-circle')
                    ->color('info')
                    ->icon('heroicon-o-plus-circle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'new'],
                    ])));
            }

            if ($visaIssued > 0) {
                $stats[] = Stat::make(
                    'تم التفييز',
                    Number::format($visaIssued)
                )
                    ->description('تم التفييز')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'visa_issued'],
                    ])));
            }

            if ($receivedContracts > 0) {
                $stats[] = Stat::make(
                    'تم الاستلام',
                    Number::format($receivedContracts)
                )
                    ->description('تم الاستلام')
                    ->descriptionIcon('heroicon-o-check-badge')
                    ->color('success')
                    ->icon('heroicon-o-check-badge')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'received'],
                    ])));
            }

            if ($returnedContracts > 0) {
                $stats[] = Stat::make(
                    'رجيع خلال فترة الضمان',
                    Number::format($returnedContracts)
                )
                    ->description('رجيع خلال فترة الضمان')
                    ->descriptionIcon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'return_during_warranty'],
                    ])));
            }

            if ($runawayContracts > 0) {
                $stats[] = Stat::make(
                    'هروب',
                    Number::format($runawayContracts)
                )
                    ->description('هروب')
                    ->descriptionIcon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'runaway'],
                    ])));
            }

            $stats[] = Stat::make(
                'إجمالي التكلفة',
                Number::currency($totalCost, 'SAR')
            )
                ->description('جميع العقود')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->url($this->buildUrl($publicUrl, $baseFilters));

            $stats[] = Stat::make(
                'المبلغ المدفوع',
                Number::currency($paidTotal, 'SAR')
            )
                ->description('المتبقي: ' . Number::currency($remainingTotal, 'SAR'))
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('info')
                ->icon('heroicon-o-arrow-trending-up')
                ->url($this->buildUrl($publicUrl, $baseFilters));

            return $stats;
        });
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
        
        $queryString = http_build_query($params);
        return $baseUrl . ($queryString ? '?' . $queryString : '');
    }
}
