<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;
use App\Filament\Widgets\Dashboard\ComplaintsStatsWidget;
use App\Filament\Widgets\Dashboard\LatestComplaintsTableWidget;
use App\Filament\Widgets\Dashboard\DashboardFilterWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesComparisonChartWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesTableWidget;
use App\Filament\Widgets\Dashboard\FinanceStatsWidget;
use App\Filament\Widgets\Dashboard\FinanceTopTypesWidget;
use App\Filament\Widgets\Dashboard\HRStatsWidget;
use App\Filament\Widgets\Dashboard\HrPendingExcuseRequestsTableWidget;
use App\Filament\Widgets\Dashboard\HrPendingLeaveRequestsTableWidget;
use App\Filament\Widgets\Dashboard\OrderStatsWidget;
use App\Filament\Widgets\Dashboard\FinancePendingApprovalStatsWidget;
use App\Filament\Widgets\Dashboard\FinancePendingApprovalTableWidget;
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

    public function getDashboardTabs(): array
    {
        $type = auth()->user()?->type;
        $filter = [DashboardFilterWidget::class];
        $tabs = [];

        $financeHeader = match ($type) {
            User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER => array_merge($filter, [
                FinanceStatsWidget::class,
                FinanceTopTypesWidget::class,
            ]),
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => array_merge($filter, [
                FinancePendingApprovalStatsWidget::class,
                FinancePendingApprovalTableWidget::class,
            ]),
            default => [],
        };
        $financeFooter = ($type === User::TYPE_SUPER_ADMIN || $type === User::TYPE_COMPANY_OWNER || $type === User::TYPE_ACCOUNTANT || $type === User::TYPE_GENERAL_ACCOUNTANT)
            ? [FinanceBranchesComparisonChartWidget::class, FinanceBranchesTableWidget::class]
            : [];
        if (! empty($financeHeader) || ! empty($financeFooter)) {
            $tabs[] = ['id' => 'finance', 'label' => 'مالية', 'widgets' => $financeHeader, 'footer' => $financeFooter];
        }

        $recruitmentHeader = match ($type) {
            User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER => [],
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => [RecruitmentAccountsTableWidget::class],
            User::TYPE_COORDINATOR => array_merge($filter, [
                RecruitmentCoordinationLatestTableWidget::class,
                RecruitmentCoordinationDelayedTableWidget::class,
            ]),
            User::TYPE_CUSTOMER_SERVICE => array_merge($filter, [RecruitmentCustomerServiceTableWidget::class]),
            default => [],
        };
        if (! empty($recruitmentHeader)) {
            $tabs[] = ['id' => 'recruitment', 'label' => 'عقود الاستقدام', 'widgets' => $recruitmentHeader, 'footer' => []];
        }

        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_COMPLAINTS_MANAGER], true)) {
            $complaints = $type === User::TYPE_COMPLAINTS_MANAGER ? array_merge($filter, [ComplaintsStatsWidget::class]) : [ComplaintsStatsWidget::class];
            if (! in_array(DashboardFilterWidget::class, $complaints)) {
                array_unshift($complaints, DashboardFilterWidget::class);
            }
            $complaints[] = LatestComplaintsTableWidget::class;
            $tabs[] = ['id' => 'complaints', 'label' => 'شكاوي', 'widgets' => $complaints, 'footer' => []];
        }

        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER], true)) {
            $tabs[] = ['id' => 'orders', 'label' => 'طلبات', 'widgets' => [OrderStatsWidget::class], 'footer' => []];
        }
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_HR_MANAGER], true)) {
            $hr = $type === User::TYPE_HR_MANAGER
                ? array_merge($filter, [HRStatsWidget::class, HrPendingLeaveRequestsTableWidget::class, HrPendingExcuseRequestsTableWidget::class])
                : [HRStatsWidget::class, HrPendingLeaveRequestsTableWidget::class, HrPendingExcuseRequestsTableWidget::class];
            $tabs[] = ['id' => 'hr', 'label' => 'موارد بشرية', 'widgets' => $hr, 'footer' => []];
        }

        if (empty($tabs)) {
            $tabs[] = ['id' => 'main', 'label' => 'الرئيسية', 'widgets' => $filter, 'footer' => []];
        }

        return $tabs;
    }

    protected function getHeaderWidgets(): array
    {
        $tabs = $this->getDashboardTabs();
        $first = $tabs[0] ?? [];
        $widgets = array_merge($first['widgets'] ?? [], $first['footer'] ?? []);

        return $widgets ?: [DashboardFilterWidget::class];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.dashboard', 'لوحة التحكم');
    }
}
