<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;
use App\Filament\Widgets\Dashboard\ComplaintsStatsWidget;
use App\Filament\Widgets\Dashboard\DashboardFilterWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesComparisonChartWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesTableWidget;
use App\Filament\Widgets\Dashboard\FinanceStatsWidget;
use App\Filament\Widgets\Dashboard\FinanceTopTypesWidget;
use App\Filament\Widgets\Dashboard\HRStatsWidget;
use App\Filament\Widgets\Dashboard\OrderStatsWidget;
use App\Filament\Widgets\Dashboard\RecruitmentContractsStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use AddsPublicToUrl;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardFilterWidget::class,
            OrderStatsWidget::class,
            FinanceStatsWidget::class,
            HRStatsWidget::class,
            RecruitmentContractsStatsWidget::class,
            ComplaintsStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            FinanceTopTypesWidget::class,
            FinanceBranchesComparisonChartWidget::class,
            FinanceBranchesTableWidget::class,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.dashboard', 'لوحة التحكم');
    }
}
