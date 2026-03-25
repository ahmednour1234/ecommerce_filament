<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;

use App\Filament\Widgets\Dashboard\LatestComplaintsTableWidget;
use App\Filament\Widgets\Dashboard\DashboardFilterWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesComparisonChartWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesTableWidget;
use App\Filament\Widgets\Dashboard\FinanceStatsWidget;
use App\Filament\Widgets\Dashboard\FinanceTopTypesWidget;
use App\Filament\Widgets\Dashboard\HRStatsWidget;
use App\Filament\Widgets\Dashboard\HrPendingExcuseRequestsTableWidget;
use App\Filament\Widgets\Dashboard\HrPendingLeaveRequestsTableWidget;
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

    public string $activeTab = '';

    public function mount(): void
    {
        $tabs = $this->getDashboardTabs();
        $this->activeTab = $tabs[0]['id'] ?? 'main';
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getDashboardTabs(): array
    {
        $type = auth()->user()?->type;
        $filter = [DashboardFilterWidget::class];
        $tabs = [];

        // ── مالية: إحصائيات ──
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER], true)) {
            $tabs[] = ['id' => 'finance_stats', 'label' => 'إحصائيات مالية', 'widgets' => array_merge($filter, [
                FinanceStatsWidget::class,
                FinanceTopTypesWidget::class,
            ]), 'footer' => []];
        } elseif (in_array($type, [User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT], true)) {
            $tabs[] = ['id' => 'finance_stats', 'label' => 'إحصائيات مالية', 'widgets' => array_merge($filter, [
                FinancePendingApprovalStatsWidget::class,
            ]), 'footer' => []];
        }

        // ── مالية: معاملات معلقة (محاسبين فقط) ──
        if (in_array($type, [User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT], true)) {
            $tabs[] = ['id' => 'finance_pending', 'label' => 'معاملات معلقة', 'widgets' => array_merge($filter, [
                FinancePendingApprovalTableWidget::class,
            ]), 'footer' => []];
        }

        // ── مالية: تقارير الفروع (مالك + سوبر) ──
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER], true)) {
            $tabs[] = ['id' => 'finance_branches', 'label' => 'تقارير الفروع', 'widgets' => array_merge($filter, [
                FinanceBranchesComparisonChartWidget::class,
                FinanceBranchesTableWidget::class,
            ]), 'footer' => []];
        }

        // ── عقود الاستقدام ──
        $recruitmentWidgets = match ($type) {
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => [RecruitmentAccountsTableWidget::class],
            User::TYPE_COORDINATOR => array_merge($filter, [
                RecruitmentCoordinationLatestTableWidget::class,
                RecruitmentCoordinationDelayedTableWidget::class,
            ]),
            User::TYPE_CUSTOMER_SERVICE => array_merge($filter, [RecruitmentCustomerServiceTableWidget::class]),
            default => [],
        };
        if (! empty($recruitmentWidgets)) {
            $tabs[] = ['id' => 'recruitment', 'label' => 'عقود الاستقدام', 'widgets' => $recruitmentWidgets, 'footer' => []];
        }

        // ── شكاوي ──
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_COMPLAINTS_MANAGER], true)) {
            $complaints = $type === User::TYPE_COMPLAINTS_MANAGER ? $filter : [DashboardFilterWidget::class];
            $complaints[] = LatestComplaintsTableWidget::class;
            $tabs[] = ['id' => 'complaints', 'label' => 'شكاوي', 'widgets' => $complaints, 'footer' => []];
        }

        // ── موارد بشرية: إحصائيات ──
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_HR_MANAGER], true)) {
            $hrFilter = $type === User::TYPE_HR_MANAGER ? $filter : [];
            $tabs[] = ['id' => 'hr_stats', 'label' => 'إحصائيات الموارد البشرية', 'widgets' => array_merge($hrFilter, [HRStatsWidget::class]), 'footer' => []];
        }

        // ── موارد بشرية: طلبات الإجازات ──
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_HR_MANAGER], true)) {
            $hrFilter = $type === User::TYPE_HR_MANAGER ? $filter : [];
            $tabs[] = ['id' => 'hr_leaves', 'label' => 'طلبات الإجازات', 'widgets' => array_merge($hrFilter, [HrPendingLeaveRequestsTableWidget::class]), 'footer' => []];
        }

        // ── موارد بشرية: طلبات الأعذار ──
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_HR_MANAGER], true)) {
            $hrFilter = $type === User::TYPE_HR_MANAGER ? $filter : [];
            $tabs[] = ['id' => 'hr_excuses', 'label' => 'طلبات الأعذار', 'widgets' => array_merge($hrFilter, [HrPendingExcuseRequestsTableWidget::class]), 'footer' => []];
        }

        if (empty($tabs)) {
            $tabs[] = ['id' => 'main', 'label' => 'الرئيسية', 'widgets' => $filter, 'footer' => []];
        }

        return $tabs;
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
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
