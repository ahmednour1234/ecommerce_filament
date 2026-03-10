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
use App\Filament\Widgets\Dashboard\RecruitmentContractsStatsWidget;
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
        if ($type === null) {
            return [
                DashboardFilterWidget::class,
                OrderStatsWidget::class,
                FinanceStatsWidget::class,
                HRStatsWidget::class,
                RecruitmentContractsStatsWidget::class,
                ComplaintsStatsWidget::class,
            ];
        }
        return match ($type) {
            User::TYPE_COORDINATOR => [
                DashboardFilterWidget::class,
                RecruitmentContractsStatsWidget::class,
                RecruitmentCoordinationLatestTableWidget::class,
                RecruitmentCoordinationDelayedTableWidget::class,
            ],
            User::TYPE_BRANCH_MANAGER => [
                DashboardFilterWidget::class,
                FinanceStatsWidget::class,
                RecruitmentContractsStatsWidget::class,
                ComplaintsStatsWidget::class,
            ],
            User::TYPE_COMPLAINTS_MANAGER => [
                DashboardFilterWidget::class,
                ComplaintsStatsWidget::class,
            ],
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => [
                DashboardFilterWidget::class,
                FinanceStatsWidget::class,
                RecruitmentAccountsTableWidget::class,
            ],
            User::TYPE_COMPANY_OWNER, User::TYPE_SUPER_ADMIN => [
                DashboardFilterWidget::class,
                OrderStatsWidget::class,
                FinanceStatsWidget::class,
                HRStatsWidget::class,
                RecruitmentContractsStatsWidget::class,
                ComplaintsStatsWidget::class,
            ],
            User::TYPE_CUSTOMER_SERVICE => [
                DashboardFilterWidget::class,
                RecruitmentCustomerServiceTableWidget::class,
            ],
            default => [
                DashboardFilterWidget::class,
                OrderStatsWidget::class,
                FinanceStatsWidget::class,
                HRStatsWidget::class,
                RecruitmentContractsStatsWidget::class,
                ComplaintsStatsWidget::class,
            ],
        };
    }

    protected function getFooterWidgets(): array
    {
        $type = auth()->user()?->type;
        if ($type === null) {
            return [
                FinanceTopTypesWidget::class,
                FinanceBranchesComparisonChartWidget::class,
                FinanceBranchesTableWidget::class,
            ];
        }
        $financeFooter = [
            FinanceTopTypesWidget::class,
            FinanceBranchesComparisonChartWidget::class,
            FinanceBranchesTableWidget::class,
        ];
        return match ($type) {
            User::TYPE_COORDINATOR, User::TYPE_COMPLAINTS_MANAGER, User::TYPE_CUSTOMER_SERVICE => [],
            User::TYPE_BRANCH_MANAGER, User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => $financeFooter,
            User::TYPE_COMPANY_OWNER, User::TYPE_SUPER_ADMIN => $financeFooter,
            default => $financeFooter,
        };
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.dashboard', 'لوحة التحكم');
    }
}
