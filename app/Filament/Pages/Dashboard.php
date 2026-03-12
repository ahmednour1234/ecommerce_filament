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
use App\Filament\Widgets\Dashboard\RecruitmentAccountsTableWidget;
use App\Filament\Widgets\Dashboard\RecruitmentCoordinationDelayedTableWidget;
use App\Filament\Widgets\Dashboard\RecruitmentCoordinationLatestTableWidget;
use App\Filament\Widgets\Dashboard\RecruitmentCustomerServiceTableWidget;
use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use AddsPublicToUrl;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        $type = auth()->user()?->type;
        return match ($type) {
            User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER => [
                DashboardFilterWidget::class,
                HRStatsWidget::class,
                ComplaintsStatsWidget::class,
            ],
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => [
                DashboardFilterWidget::class,
                RecruitmentAccountsTableWidget::class,
            ],
            User::TYPE_COORDINATOR => [
                DashboardFilterWidget::class,
                RecruitmentCoordinationLatestTableWidget::class,
                RecruitmentCoordinationDelayedTableWidget::class,
            ],
            User::TYPE_BRANCH_MANAGER => [
                DashboardFilterWidget::class,
            ],
            User::TYPE_COMPLAINTS_MANAGER => [
                DashboardFilterWidget::class,
                ComplaintsStatsWidget::class,
            ],
            User::TYPE_HR_MANAGER => [
                DashboardFilterWidget::class,
                HRStatsWidget::class,
            ],
            User::TYPE_CUSTOMER_SERVICE => [
                DashboardFilterWidget::class,
                RecruitmentCustomerServiceTableWidget::class,
            ],
            default => [
                DashboardFilterWidget::class,
            ],
        };
    }

    protected function getFooterWidgets(): array
    {
        $type = auth()->user()?->type;
        $financeFooter = [
            FinanceBranchesComparisonChartWidget::class,
            FinanceBranchesTableWidget::class,
        ];
        return match ($type) {
            User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER => $financeFooter,
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => $financeFooter,
            default => [],
        };
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.dashboard', 'لوحة التحكم');
    }
}
