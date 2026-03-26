<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;
use App\Filament\Widgets\Dashboard\ComplaintsStatsWidget;
use App\Filament\Widgets\Dashboard\DashboardFilterWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesComparisonChartWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesTableWidget;
use App\Filament\Widgets\Dashboard\FinancePendingApprovalStatsWidget;
use App\Filament\Widgets\Dashboard\FinancePendingApprovalTableWidget;
use App\Filament\Widgets\Dashboard\FinanceStatsWidget;
use App\Filament\Widgets\Dashboard\FinanceTopTypesWidget;
use App\Filament\Widgets\Dashboard\HRStatsWidget;
use App\Filament\Widgets\Dashboard\HrPendingExcuseRequestsTableWidget;
use App\Filament\Widgets\Dashboard\HrPendingLeaveRequestsTableWidget;
use App\Filament\Widgets\Dashboard\LatestComplaintsTableWidget;
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

    /**
     * Returns the 4 dashboard sections:
     * 1. المالية  2. الشكاوي  3. عقود الاستقدام  4. الموارد البشرية
     * Each section has rows; each row is either full-width or a 2-column pair.
     */
    public function getDashboardSections(): array
    {
        $type = auth()->user()?->type;
        $sections = [];

        // ─────────────────────────────────────
        // Section 1: المالية
        // ─────────────────────────────────────
        $financeRows = [];
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER], true)) {
            $financeRows = [
                // Two stat widgets side-by-side
                ['pair' => true, 'widgets' => [FinanceStatsWidget::class, FinancePendingApprovalStatsWidget::class]],
                // Two charts side-by-side
                ['pair' => true, 'widgets' => [FinanceBranchesComparisonChartWidget::class, FinanceTopTypesWidget::class]],
                // Two tables side-by-side
                ['pair' => true, 'widgets' => [FinanceBranchesTableWidget::class, FinancePendingApprovalTableWidget::class]],
            ];
        } elseif (in_array($type, [User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT], true)) {
            $financeRows = [
                ['pair' => false, 'widgets' => [FinancePendingApprovalStatsWidget::class]],
                ['pair' => false, 'widgets' => [FinancePendingApprovalTableWidget::class]],
            ];
        }
        if (! empty($financeRows)) {
            $sections[] = [
                'id'    => 'finance',
                'label' => 'المالية',
                'icon'  => 'heroicon-o-banknotes',
                'rows'  => $financeRows,
            ];
        }

        // ─────────────────────────────────────
        // Section 2: الشكاوي
        // ─────────────────────────────────────
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_COMPLAINTS_MANAGER], true)) {
            $sections[] = [
                'id'    => 'complaints',
                'label' => 'الشكاوي',
                'icon'  => 'heroicon-o-chat-bubble-left-ellipsis',
                'rows'  => [
                    ['pair' => false, 'widgets' => [ComplaintsStatsWidget::class]],
                    ['pair' => false, 'widgets' => [LatestComplaintsTableWidget::class]],
                ],
            ];
        }

        // ─────────────────────────────────────
        // Section 3: عقود الاستقدام
        // ─────────────────────────────────────
        $recruitmentRows = [];
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_COORDINATOR], true)) {
            $recruitmentRows = [
                ['pair' => true, 'widgets' => [
                    RecruitmentCoordinationLatestTableWidget::class,
                    RecruitmentCoordinationDelayedTableWidget::class,
                ]],
            ];
        } elseif ($type === User::TYPE_CUSTOMER_SERVICE) {
            $recruitmentRows = [
                ['pair' => false, 'widgets' => [RecruitmentCustomerServiceTableWidget::class]],
            ];
        } elseif (in_array($type, [User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT], true)) {
            $recruitmentRows = [
                ['pair' => false, 'widgets' => [RecruitmentAccountsTableWidget::class]],
            ];
        }
        if (! empty($recruitmentRows)) {
            $sections[] = [
                'id'    => 'recruitment',
                'label' => 'عقود الاستقدام',
                'icon'  => 'heroicon-o-document-text',
                'rows'  => $recruitmentRows,
            ];
        }

        // ─────────────────────────────────────
        // Section 4: الموارد البشرية
        // ─────────────────────────────────────
        if (in_array($type, [User::TYPE_SUPER_ADMIN, User::TYPE_COMPANY_OWNER, User::TYPE_HR_MANAGER], true)) {
            $sections[] = [
                'id'    => 'hr',
                'label' => 'الموارد البشرية',
                'icon'  => 'heroicon-o-users',
                'rows'  => [
                    ['pair' => false, 'widgets' => [HRStatsWidget::class]],
                    ['pair' => true, 'widgets' => [
                        HrPendingLeaveRequestsTableWidget::class,
                        HrPendingExcuseRequestsTableWidget::class,
                    ]],
                ],
            ];
        }

        if (empty($sections)) {
            $sections[] = [
                'id'    => 'main',
                'label' => 'الرئيسية',
                'icon'  => 'heroicon-o-home',
                'rows'  => [],
            ];
        }

        return $sections;
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
