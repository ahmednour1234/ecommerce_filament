<?php

namespace App\Providers\Filament;

use App\Services\MainCore\ThemeService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// HR Resources / Pages
use App\Filament\Resources\HR\DepartmentResource;
use App\Filament\Resources\HR\EmployeeResource;
use App\Filament\Resources\HR\WorkScheduleResource;
use App\Filament\Resources\HR\LeaveTypeResource;
use App\Filament\Resources\HR\LoanTypeResource;
use App\Filament\Resources\HR\SalaryComponentResource;
use App\Filament\Resources\HR\ExcuseRequestResource;
use App\Filament\Pages\HR\LeaveReportPage;
use App\Filament\Pages\HR\MonthlyAttendanceReportPage;

// Recruitment Contracts
use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use App\Filament\Pages\Recruitment\ReceivedWorkersPage;
use App\Filament\Pages\Recruitment\ExpiredContractsPage;
use App\Filament\Pages\Recruitment\RecruitmentContractsReportsPage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        /** @var ThemeService $themeService */
        $themeService = app(ThemeService::class);

        $theme = $themeService->defaultTheme();

        $primaryHex   = $theme?->primary_color   ?: '#F59E0B';
        $secondaryHex = $theme?->secondary_color ?: '#0EA5E9';
        $accentHex    = $theme?->accent_color    ?: '#22C55E';

        $brandName = setting('app.name', 'MainCore Dashboard');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            ->brandName($brandName)
            ->brandLogo(fn () => $theme?->logo_light_url ?? null)

            ->colors([
                'primary'   => Color::hex($primaryHex),
                'secondary' => Color::hex($secondaryHex),
                'accent'    => Color::hex($accentHex),
                'info'      => Color::hex($accentHex),
                'success'   => Color::hex($accentHex),
                'warning'   => Color::Amber,
                'danger'    => Color::Rose,
            ])

            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources',
            )

            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages',
            )
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Resources\Finance\Reports\Pages\FinanceIncomeExpenseReport::class,
            ])

            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets',
            )
            ->widgets([
                Widgets\AccountWidget::class,
            ])

            // ✅ User menu items (Profile) - stays in user menu, but we will force it visually LAST via CSS.
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => app(\App\Services\MainCore\TranslationService::class)
                        ->get('navigation.my_profile', null, 'dashboard', 'My Profile'))
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => \App\Filament\Pages\UserProfile::getUrl())
                    ->sort(10),
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
            ])

            ->authMiddleware([
                Authenticate::class,
            ])

            ->navigationItems([
                NavigationItem::make('section-basic-settings')
                    ->label('الإعدادات الأساسية')
                    ->group('الموارد البشرية')
                    ->sort(105)
                    ->url(fn () => DepartmentResource::getUrl())
                    ->icon(null),

                NavigationItem::make('section-employee-management')
                    ->label('إدارة الموظفين')
                    ->group('الموارد البشرية')
                    ->sort(205)
                    ->url(fn () => EmployeeResource::getUrl())
                    ->icon(null),

                NavigationItem::make('section-attendance')
                    ->label('الحضور والانصراف')
                    ->group('الموارد البشرية')
                    ->sort(305)
                    ->url(fn () => WorkScheduleResource::getUrl())
                    ->icon(null),

                NavigationItem::make('section-leaves-holidays')
                    ->label('الإجازات والعطلات')
                    ->group('الموارد البشرية')
                    ->sort(405)
                    ->url(fn () => LeaveTypeResource::getUrl())
                    ->icon(null),

                NavigationItem::make('section-loans')
                    ->label('القروض والسلف')
                    ->group('الموارد البشرية')
                    ->sort(505)
                    ->url(fn () => LoanTypeResource::getUrl())
                    ->icon(null),

                NavigationItem::make('section-salaries')
                    ->label('الرواتب والمستحقات')
                    ->group('الموارد البشرية')
                    ->sort(605)
                    ->url(fn () => SalaryComponentResource::getUrl())
                    ->icon(null),

                NavigationItem::make('section-requests')
                    ->label('الطلبات')
                    ->group('الموارد البشرية')
                    ->sort(705)
                    ->url(fn () => ExcuseRequestResource::getUrl())
                    ->icon(null),

                NavigationItem::make('section-reports')
                    ->label('التقارير')
                    ->group('الموارد البشرية')
                    ->sort(805)
                    ->url(fn () => LeaveReportPage::getUrl())
                    ->icon(null),

                NavigationItem::make('leave-report-reports')
                    ->label(fn () => tr('navigation.hr_leave_report', [], null, 'dashboard') ?: 'تقرير الإجازات')
                    ->group('الموارد البشرية')
                    ->sort(810)
                    ->url(fn () => LeaveReportPage::getUrl())
                    ->icon('heroicon-o-chart-bar')
                    ->visible(fn () => auth()->user()?->can('hr_leave_reports.view') ?? false),

                NavigationItem::make('monthly-attendance-report-reports')
                    ->label(fn () => tr('navigation.hr_monthly_attendance_report', [], null, 'dashboard') ?: 'تقرير الحضور الشهري')
                    ->group('الموارد البشرية')
                    ->sort(820)
                    ->url(fn () => MonthlyAttendanceReportPage::getUrl())
                    ->icon('heroicon-o-chart-bar')
                    ->visible(fn () => auth()->user()?->can('hr_attendance_monthly.view') ?? false),

            ]);
    }

    public function register(): void
    {
        parent::register();

        /**
         * ✅ Insert Global Search into topbar actions.
         * We keep it in TOPBAR_END so it stays near the user menu,
         * then we force ordering via CSS so Profile becomes LAST visually.
         */
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_END,
            fn (): string => view('filament.components.global-search')->render(),
        );

        /**
         * ✅ Global styling fixes:
         * - Hide table search (temporary) – better to disable per Resource later.
         * - Make topbar actions a flex row with RTL row-reverse.
         * - Ensure Search comes first, Profile comes last (in both LTR/RTL).
         */
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => <<<'HTML'
<style>
/* -------------------------------------------------------
   1) Hide Filament table search (prefer disabling per Resource)
-------------------------------------------------------- */
.fi-ta-search-field,
.fi-ta-header-actions .fi-input-wrp input[type="search"],
.fi-ta-header-actions input[type="search"],
.fi-ta-header-actions input[placeholder*="Search"],
.fi-ta-header-actions input[placeholder*="بحث"] {
    display: none !important;
}

/* -------------------------------------------------------
   2) Topbar actions layout
   Use stable class: .fi-topbar-actions (Filament v3)
-------------------------------------------------------- */
.fi-topbar .fi-topbar-actions {
    display: flex !important;
    align-items: center !important;
    gap: .75rem !important;
}

/* RTL: reverse visual flow so the "last" item is on the far left */
[dir="rtl"] .fi-topbar .fi-topbar-actions {
    flex-direction: row-reverse !important;
}

/* -------------------------------------------------------
   3) Force ordering:
   - Global search should come BEFORE profile (order:1)
   - Profile menu should be visually LAST (order:99)
   Notes:
   - In RTL, row-reverse makes LAST appear on the far left (as requested).
-------------------------------------------------------- */

/* Our global search wrapper in topbar */
.fi-topbar .fi-topbar-actions [data-global-search] {
    order: 1 !important;
}

/* Try to catch Filament user menu button/container */
.fi-topbar .fi-topbar-actions [data-user-menu],
.fi-topbar .fi-topbar-actions button[aria-label*="user"],
.fi-topbar .fi-topbar-actions button[aria-label*="User"],
.fi-topbar .fi-topbar-actions [aria-label*="user menu"] {
    order: 99 !important;
}
</style>
HTML
        );
    }
}
