<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Pages\Dashboard;
use App\Services\Dashboard\DashboardService;
use App\Support\Money;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class FinanceStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected ?string $heading = 'إحصائيات المالية';

    protected function getStats(): array
    {
        $filters = \App\Helpers\DashboardFilterHelper::parseFiltersFromRequest();
        $service = app(DashboardService::class);
        $kpis = $service->getFinancialKpis($filters);

        if ($kpis['total_revenue'] == 0 && $kpis['total_expenses'] == 0) {
            return [
                Stat::make('لا توجد بيانات', 'لا توجد معاملات مالية في الفترة المحددة')
                    ->description('')
                    ->color('gray')
                    ->icon('heroicon-o-information-circle'),
            ];
        }

        $defaultCurrencyId = Money::defaultCurrencyId();

        return [
            Stat::make('إجمالي الإيرادات', Money::format($kpis['total_revenue'], $defaultCurrencyId))
                ->description('عدد المعاملات: ' . Number::format($kpis['revenue_count']))
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('إجمالي المصروفات', Money::format($kpis['total_expenses'], $defaultCurrencyId))
                ->description('عدد المعاملات: ' . Number::format($kpis['expense_count']))
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('صافي الربح', Money::format($kpis['net_profit'], $defaultCurrencyId))
                ->description($kpis['net_profit'] >= 0 ? 'ربح إيجابي' : 'خسارة')
                ->descriptionIcon($kpis['net_profit'] >= 0 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                ->color($kpis['net_profit'] >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-chart-bar'),

            Stat::make('عدد معاملات الإيرادات', Number::format($kpis['revenue_count']))
                ->description('معاملات الإيرادات في الفترة المحددة')
                ->descriptionIcon('heroicon-o-document-plus')
                ->color('info')
                ->icon('heroicon-o-document-text'),

            Stat::make('عدد معاملات المصروفات', Number::format($kpis['expense_count']))
                ->description('معاملات المصروفات في الفترة المحددة')
                ->descriptionIcon('heroicon-o-document-minus')
                ->color('warning')
                ->icon('heroicon-o-document-duplicate'),
        ];
    }
}
