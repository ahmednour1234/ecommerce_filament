<?php

namespace App\Providers\Filament;

use App\Services\MainCore\ThemeService;
use App\Services\MainCore\TranslationService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
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

class AdminPanelProvider extends PanelProvider
{
    private function navLabel(string $key, string $fallback): \Closure
    {
        return function () use ($key, $fallback) {
            try {
                $translationService = app(TranslationService::class);

                // Always try Arabic first (priority)
                $arabicTranslation = $translationService->get($key, 'ar', 'dashboard', null);

                // If we got a valid translation (not the key itself and not empty), return it
                if ($arabicTranslation && $arabicTranslation !== $key && $arabicTranslation !== '' && $arabicTranslation !== null) {
                    return $arabicTranslation;
                }

                // Fallback to provided Arabic text
                return $fallback;
            } catch (\Exception $e) {
                // If anything fails, return fallback
                return $fallback;
            }
        };
    }

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

            // User menu (profile) - stays in user menu
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn () => app(TranslationService::class)
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

            /**
             * IMPORTANT:
             * These are GROUP KEYS, not Arabic labels.
             * Your Resources/Pages MUST use the same key in $navigationGroup.
             */
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make('recruitment_contracts')
                    ->label($this->navLabel('sidebar.recruitment_contracts', 'عقود الاستقدام'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('housing')
                    ->label($this->navLabel('sidebar.housing', 'الإيواء'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('rental')
                    ->label($this->navLabel('sidebar.rental', 'قسم التأجير'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('service_transfer')
                    ->label($this->navLabel('sidebar.service_transfer', 'نقل الخدمات'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('packages')
                    ->label($this->navLabel('sidebar.packages', 'باقات العروض'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('candidates')
                    ->label($this->navLabel('sidebar.candidates', 'المرشحين'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('clients')
                    ->label($this->navLabel('sidebar.clients', 'العملاء'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('agents')
                    ->label($this->navLabel('sidebar.agents', 'الوكلاء'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('finance')
                    ->label($this->navLabel('sidebar.finance', 'قسم الحسابات'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('follow_up')
                    ->label($this->navLabel('sidebar.follow_up', 'المتابعة'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('messages')
                    ->label($this->navLabel('sidebar.messages', 'قسم الرسائل'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('company_visas')
                    ->label($this->navLabel('sidebar.company_visas', 'تأشيرات الشركة'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('app_management')
                    ->label($this->navLabel('sidebar.app_management', 'إدارة التطبيق'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('profile')
                    ->label($this->navLabel('sidebar.profile', 'الملف الشخصي'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('employee_commissions')
                    ->label($this->navLabel('sidebar.employee_commissions', 'عمولات الموظفين'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('hr')
                    ->label($this->navLabel('sidebar.hr', 'الموارد البشرية'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('system_movement')
                    ->label($this->navLabel('sidebar.system_movement', 'حركة النظام المرجعي'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('notifications')
                    ->label($this->navLabel('sidebar.notifications', 'التنبيهات'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('settings')
                    ->label($this->navLabel('sidebar.settings', 'الإعدادات'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('branches')
                    ->label($this->navLabel('sidebar.branches', 'الفروع'))
                    ->collapsible(false),

                \Filament\Navigation\NavigationGroup::make('website_management')
                    ->label($this->navLabel('sidebar.website_management', 'إدارة الموقع'))
                    ->collapsible(false),
            ]);
    }

    public function register(): void
    {
        parent::register();

        // Global search in topbar
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_END,
            fn (): string => view('filament.components.global-search')->render(),
        );

        // Global styles
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => <<<'HTML'
<style>
/* Hide Filament table search (temporary) */
.fi-ta-search-field:not(.show-search),
.fi-ta-header-actions .fi-input-wrp input[type="search"]:not(.show-search),
.fi-ta-header-actions input[type="search"]:not(.show-search),
.fi-ta-header-actions input[placeholder*="Search"]:not(.show-search),
.fi-ta-header-actions input[placeholder*="بحث"]:not(.show-search) {
    display: none !important;
}

/* Show search for recruitment contracts */
body:has([href*="recruitment-contracts"]) .fi-ta-search-field,
body:has([href*="recruitment-contracts"]) .fi-ta-header-actions .fi-input-wrp input[type="search"],
body:has([href*="recruitment-contracts"]) .fi-ta-header-actions input[type="search"],
.fi-ta-search-field.show-search,
.fi-ta-header-actions .fi-input-wrp input[type="search"].show-search,
.fi-ta-header-actions input[type="search"].show-search {
    display: block !important;
}

.fi-topbar .fi-topbar-actions {
    display: flex !important;
    align-items: center !important;
    gap: .75rem !important;
}

[dir="rtl"] .fi-topbar .fi-topbar-actions {
    flex-direction: row-reverse !important;
}

.fi-topbar .fi-topbar-actions [data-global-search] {
    order: 1 !important;
}

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
