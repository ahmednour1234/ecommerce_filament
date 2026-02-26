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
use Illuminate\Support\Str;

class AdminPanelProvider extends PanelProvider
{
    protected static function addPublicToUrl(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        if (str_contains($path, '/admin/') && !str_contains($path, '/public/admin/')) {
            if (str_starts_with($path, '/public/')) {
                $path = substr($path, 7);
            }
            $newPath = str_replace('/admin/', '/public/admin/', $path);

            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

            return $scheme . '://' . $host . $newPath . $query . $fragment;
        }

        return $url;
    }

    private function navLabel(string $key, string $fallback): string
    {
        // Use tr() helper with Arabic locale first
        $translation = tr($key, [], 'ar', 'dashboard');

        // If translation found and not the key itself, return it
        if ($translation && $translation !== $key && $translation !== '') {
            return $translation;
        }

        // Fallback to provided Arabic text
        return $fallback;
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
            ->login(\App\Filament\Pages\Auth\Login::class)

            ->brandName($brandName)
            ->brandLogo(function () use ($theme) {
                // Get logo from settings dynamically
                $appLogo = \App\Models\MainCore\Setting::where('key', 'app.name')->first()?->logo;

                if ($appLogo) {
                    $baseUrl = rtrim(config('app.url'), '/');
                    $baseUrl = str_replace('/public', '', $baseUrl);
                    return $baseUrl . '/storage/app/public/' . ltrim($appLogo, '/');
                }

                // Fallback to theme logo
                return $theme?->logo_light_url ?? null;
            })
            ->brandLogoHeight('3.5rem')

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
                \Filament\Navigation\NavigationGroup::make('عقود الاستقدام')
                    ->label('عقود الاستقدام')
                    ->collapsible(true),


                \Filament\Navigation\NavigationGroup::make('إيواء الاستقدام')
                    ->label('إيواء الاستقدام')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('إيواء التأجير')
                    ->label('إيواء التأجير')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('قسم التأجير')
                    ->label('قسم التأجير')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('نقل الخدمات')
                    ->label('نقل الخدمات')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('باقات العروض')
                    ->label('باقات العروض')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('المرشحين')
                    ->label('المرشحين')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('العملاء')
                    ->label('العملاء')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('الوكلاء')
                    ->label('الوكلاء')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('قسم الحسابات')
                    ->label('قسم الحسابات')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('المتابعة')
                    ->label('المتابعة')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('قسم الرسائل')
                    ->label('قسم الرسائل')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('تأشيرات الشركة')
                    ->label('تأشيرات الشركة')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('إدارة التطبيق')
                    ->label('إدارة التطبيق')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('الملف الشخصي')
                    ->label('الملف الشخصي')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('عمولات الموظفين')
                    ->label('عمولات الموظفين')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('الموارد البشرية')
                    ->label('الموارد البشرية')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('حركة النظام المرجعي')
                    ->label('حركة النظام المرجعي')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('التنبيهات')
                    ->label('التنبيهات')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('الإعدادات')
                    ->label('الإعدادات')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('الفروع')
                    ->label('الفروع')
                    ->collapsible(true),

                \Filament\Navigation\NavigationGroup::make('إدارة الموقع')
                    ->label('إدارة الموقع')
                    ->collapsible(true),
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

/* Logo styling - make it bigger, clearer, and wider */
.fi-logo {
    height: 3.5rem !important;
    width: auto !important;
    min-width: 180px !important;
    max-width: 280px !important;
    object-fit: contain !important;
    image-rendering: -webkit-optimize-contrast !important;
    image-rendering: crisp-edges !important;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1)) !important;
    transition: all 0.3s ease !important;
}

.fi-logo:hover {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15)) !important;
    transform: scale(1.02) !important;
}

.fi-sidebar-header .fi-logo {
    padding: 0.5rem 0 !important;
}
</style>
HTML
        );
    }
}
