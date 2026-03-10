<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class RecruitmentContractsStatsSimpleWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'عقود الاستقدام';

    protected $listeners = ['filters-updated' => '$refresh'];

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
        $from = $filters['date_from'] ?? null;
        $to = $filters['date_to'] ?? null;
        $query = RecruitmentContract::query();
        if ($from && $to) {
            if (is_string($from)) {
                $from = Carbon::parse($from)->startOfDay();
            }
            if (is_string($to)) {
                $to = Carbon::parse($to)->endOfDay();
            }
            $query->whereBetween('created_at', [$from, $to]);
        }
        if (! empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        $total = $query->count();
        $new = (clone $query)->where('status', 'new')->count();
        $received = (clone $query)->where('status', 'received')->count();
        $processing = $total - $new - $received;

        $baseUrl = RecruitmentContractResource::getUrl('index');
        $baseFilters = $from && $to ? ['created_at' => ['created_from' => $from->format('Y-m-d'), 'created_until' => $to->format('Y-m-d')]] : [];

        return [
            Stat::make('الإجمالي', Number::format($total))
                ->description('عقود الاستقدام')
                ->color('primary')
                ->url($baseUrl)
                ->extraAttributes(['class' => 'recruitment-stat-card-simple']),
            Stat::make('جديد', Number::format($new))
                ->description('عقود جديدة')
                ->color('info')
                ->url($baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'tableFilters[status][value]=new')
                ->extraAttributes(['class' => 'recruitment-stat-card-simple']),
            Stat::make('تم الاستلام', Number::format($received))
                ->description('تم التسليم')
                ->color('success')
                ->url($baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'tableFilters[status][value]=received')
                ->extraAttributes(['class' => 'recruitment-stat-card-simple']),
            Stat::make('قيد المعالجة', Number::format($processing))
                ->description('غير منتهية')
                ->color('warning')
                ->extraAttributes(['class' => 'recruitment-stat-card-simple']),
        ];
    }
}
