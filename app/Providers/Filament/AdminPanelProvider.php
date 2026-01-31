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
use App\Filament\Resources\HR\DepartmentResource;
use App\Filament\Resources\HR\EmployeeResource;
use App\Filament\Resources\HR\WorkScheduleResource;
use App\Filament\Resources\HR\LeaveTypeResource;
use App\Filament\Resources\HR\LoanTypeResource;
use App\Filament\Resources\HR\SalaryComponentResource;
use App\Filament\Resources\HR\ExcuseRequestResource;
use App\Filament\Pages\HR\LeaveReportPage;
use App\Filament\Pages\HR\MonthlyAttendanceReportPage;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        /** @var ThemeService $themeService */
        $themeService = app(ThemeService::class);

        // Theme الافتراضي من السيرفس (اللي فيه primary_color, secondary_color, accent_color, logo_light, ...)
        $theme = $themeService->defaultTheme();

        // 🟡 ألوان جايّة من الداتابيز (ColorPicker)
        $primaryHex   = $theme?->primary_color   ?: '#F59E0B'; // Amber
        $secondaryHex = $theme?->secondary_color ?: '#0EA5E9'; // Sky
        $accentHex    = $theme?->accent_color    ?: '#22C55E'; // Green

        // 🧠 brandName من setting
        $brandName = setting('app.name', 'MainCore Dashboard');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // 🏷️ اسم اللوحة
            ->brandName($brandName)

            // 🖼️ اللوجو الخفيف (light) من الـ theme
            ->brandLogo(fn () => $theme?->logo_light_url ?? null)

            // 🎨 ألوان Filament
            ->colors([
                'primary'   => Color::hex($primaryHex),
                'secondary' => Color::hex($secondaryHex),
                'accent'    => Color::hex($accentHex),
                'info'      => Color::hex($accentHex),
                'success'   => Color::hex($accentHex),
                'warning'   => Color::Amber,
                'danger'    => Color::Rose,
            ])

            // Resources
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources',
            )

            // Pages
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages',
            )
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Resources\Finance\Reports\Pages\FinanceIncomeExpenseReport::class,
            ])

            // Widgets
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets',
            )
            ->widgets([
                Widgets\AccountWidget::class,
            ])

            // User Menu Items (Navbar)
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => app(\App\Services\MainCore\TranslationService::class)
                        ->get('navigation.my_profile', null, 'dashboard', 'My Profile'))
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => \App\Filament\Pages\UserProfile::getUrl())
                    ->sort(10),
            ])

            // Middleware
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

            // Auth Middleware
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('section-basic-settings')
                    ->label('الإعدادات الأساسية')
                    ->group('الموارد البشرية')
                    ->sort(105)
                    ->url(fn() => DepartmentResource::getUrl())
                    ->icon(null),
                NavigationItem::make('section-employee-management')
                    ->label('إدارة الموظفين')
                    ->group('الموارد البشرية')
                    ->sort(205)
                    ->url(fn() => EmployeeResource::getUrl())
                    ->icon(null),
                NavigationItem::make('section-attendance')
                    ->label('الحضور والانصراف')
                    ->group('الموارد البشرية')
                    ->sort(305)
                    ->url(fn() => WorkScheduleResource::getUrl())
                    ->icon(null),
                NavigationItem::make('section-leaves-holidays')
                    ->label('الإجازات والعطلات')
                    ->group('الموارد البشرية')
                    ->sort(405)
                    ->url(fn() => LeaveTypeResource::getUrl())
                    ->icon(null),
                NavigationItem::make('section-loans')
                    ->label('القروض والسلف')
                    ->group('الموارد البشرية')
                    ->sort(505)
                    ->url(fn() => LoanTypeResource::getUrl())
                    ->icon(null),
                NavigationItem::make('section-salaries')
                    ->label('الرواتب والمستحقات')
                    ->group('الموارد البشرية')
                    ->sort(605)
                    ->url(fn() => SalaryComponentResource::getUrl())
                    ->icon(null),
                NavigationItem::make('section-requests')
                    ->label('الطلبات')
                    ->group('الموارد البشرية')
                    ->sort(705)
                    ->url(fn() => ExcuseRequestResource::getUrl())
                    ->icon(null),
                NavigationItem::make('section-reports')
                    ->label('التقارير')
                    ->group('الموارد البشرية')
                    ->sort(805)
                    ->url(fn() => LeaveReportPage::getUrl())
                    ->icon(null),
                NavigationItem::make('leave-report-reports')
                    ->label(fn() => tr('navigation.hr_leave_report', [], null, 'dashboard') ?: 'تقرير الإجازات')
                    ->group('الموارد البشرية')
                    ->sort(810)
                    ->url(fn() => LeaveReportPage::getUrl())
                    ->icon('heroicon-o-chart-bar')
                    ->visible(fn() => auth()->user()?->can('hr_leave_reports.view') ?? false),
                NavigationItem::make('monthly-attendance-report-reports')
                    ->label(fn() => tr('navigation.hr_monthly_attendance_report', [], null, 'dashboard') ?: 'تقرير الحضور الشهري')
                    ->group('الموارد البشرية')
                    ->sort(820)
                    ->url(fn() => MonthlyAttendanceReportPage::getUrl())
                    ->icon('heroicon-o-chart-bar')
                    ->visible(fn() => auth()->user()?->can('hr_attendance_monthly.view') ?? false),
            ]);
    }

    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_AFTER,
            fn (): string => view('filament.components.sidebar-search')->render(),
        );
    }
}
