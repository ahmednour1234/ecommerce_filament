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
        return tr('recruitment_contract.dashboard.heading', [], null, 'dashboard') ?: 'إحصائيات عقود الاستقدام';
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
            $publicUrl = '/public' . $baseUrl;
            $query = RecruitmentContract::query()
                ->whereBetween('created_at', [$from, $to]);

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $totalContracts = $query->count();
            $newContracts = (clone $query)->where('status', 'new')->count();
            $processingContracts = (clone $query)->where('status', 'processing')->count();
            $visaIssued = (clone $query)->where('status', 'visa_issued')->count();
            $arrived = (clone $query)->where('status', 'arrived_in_saudi_arabia')->count();
            $closed = (clone $query)->where('status', 'closed')->count();
            $rejected = (clone $query)->where('status', 'rejected')->count();

            $totalCost = (clone $query)->sum('total_cost');
            $paidTotal = (clone $query)->sum('paid_total');
            $remainingTotal = (clone $query)->sum('remaining_total');

            $stats = [];

            if ($totalContracts === 0) {
                return [
                    Stat::make(
                        tr('recruitment_contract.dashboard.no_data', [], null, 'dashboard') ?: 'لا توجد بيانات',
                        tr('recruitment_contract.dashboard.no_contracts_period', [], null, 'dashboard') ?: 'لا توجد عقود في الفترة المحددة'
                    )
                        ->description('')
                        ->color('gray')
                        ->icon('heroicon-o-information-circle'),
                ];
            }

            $stats[] = Stat::make(
                tr('recruitment_contract.dashboard.total_contracts', [], null, 'dashboard') ?: 'إجمالي العقود',
                Number::format($totalContracts)
            )
                ->description(tr('recruitment_contract.dashboard.in_period', [], null, 'dashboard') ?: 'في الفترة المحددة')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->icon('heroicon-o-document-text')
                ->url($this->buildUrl($publicUrl, $baseFilters));

            if ($newContracts > 0) {
                $stats[] = Stat::make(
                    tr('recruitment_contract.dashboard.new_contracts', [], null, 'dashboard') ?: 'عقود جديدة',
                    Number::format($newContracts)
                )
                    ->description(tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'جديد')
                    ->descriptionIcon('heroicon-o-plus-circle')
                    ->color('info')
                    ->icon('heroicon-o-plus-circle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'new'],
                    ])));
            }

            if ($processingContracts > 0) {
                $stats[] = Stat::make(
                    tr('recruitment_contract.dashboard.processing_contracts', [], null, 'dashboard') ?: 'عقود قيد المعالجة',
                    Number::format($processingContracts)
                )
                    ->description(tr('recruitment_contract.status.processing', [], null, 'dashboard') ?: 'قيد المعالجة')
                    ->descriptionIcon('heroicon-o-clock')
                    ->color('warning')
                    ->icon('heroicon-o-clock')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'processing'],
                    ])));
            }

            if ($visaIssued > 0) {
                $stats[] = Stat::make(
                    tr('recruitment_contract.dashboard.visa_issued', [], null, 'dashboard') ?: 'تم إصدار التأشيرة',
                    Number::format($visaIssued)
                )
                    ->description(tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم إصدار التأشيرة')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'visa_issued'],
                    ])));
            }

            if ($arrived > 0) {
                $stats[] = Stat::make(
                    tr('recruitment_contract.dashboard.arrived', [], null, 'dashboard') ?: 'وصل للمملكة',
                    Number::format($arrived)
                )
                    ->description(tr('recruitment_contract.status.arrived_in_saudi_arabia', [], null, 'dashboard') ?: 'وصل للمملكة العربية السعودية')
                    ->descriptionIcon('heroicon-o-map-pin')
                    ->color('success')
                    ->icon('heroicon-o-map-pin')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'arrived_in_saudi_arabia'],
                    ])));
            }

            if ($closed > 0) {
                $stats[] = Stat::make(
                    tr('recruitment_contract.dashboard.closed_contracts', [], null, 'dashboard') ?: 'عقود مغلقة',
                    Number::format($closed)
                )
                    ->description(tr('recruitment_contract.status.closed', [], null, 'dashboard') ?: 'مغلق')
                    ->descriptionIcon('heroicon-o-check-badge')
                    ->color('gray')
                    ->icon('heroicon-o-check-badge')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'closed'],
                    ])));
            }

            if ($rejected > 0) {
                $stats[] = Stat::make(
                    tr('recruitment_contract.dashboard.rejected_contracts', [], null, 'dashboard') ?: 'عقود مرفوضة',
                    Number::format($rejected)
                )
                    ->description(tr('recruitment_contract.status.rejected', [], null, 'dashboard') ?: 'مرفوض')
                    ->descriptionIcon('heroicon-o-x-circle')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->url($this->buildUrl($publicUrl, array_merge($baseFilters, [
                        'status' => ['value' => 'rejected'],
                    ])));
            }

            $stats[] = Stat::make(
                tr('recruitment_contract.dashboard.total_cost', [], null, 'dashboard') ?: 'إجمالي التكلفة',
                Number::currency($totalCost, 'SAR')
            )
                ->description(tr('recruitment_contract.dashboard.all_contracts', [], null, 'dashboard') ?: 'جميع العقود')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->url($this->buildUrl($publicUrl, $baseFilters));

            $remainingLabel = tr('recruitment_contract.dashboard.remaining', [], null, 'dashboard') ?: 'المتبقي';
            $stats[] = Stat::make(
                tr('recruitment_contract.dashboard.paid_total', [], null, 'dashboard') ?: 'المبلغ المدفوع',
                Number::currency($paidTotal, 'SAR')
            )
                ->description($remainingLabel . ': ' . Number::currency($remainingTotal, 'SAR'))
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('info')
                ->icon('heroicon-o-arrow-trending-up')
                ->url($this->buildUrl($publicUrl, $baseFilters));

            return $stats;
        });
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
