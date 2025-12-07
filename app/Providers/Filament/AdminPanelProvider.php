<?php

namespace App\Providers\Filament;

use App\Services\MainCore\ThemeService;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
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
        // لو مش موجودة في الداتابيز، نحط قيم افتراضية محترمة
        $primaryHex   = $theme?->primary_color   ?: '#F59E0B'; // Amber
        $secondaryHex = $theme?->secondary_color ?: '#0EA5E9'; // Sky
        $accentHex    = $theme?->accent_color    ?: '#22C55E'; // Green

        // 🧠 ممكن كمان تعمل brandName من setting
        $brandName = setting('app.name', 'MainCore Dashboard');

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()

            // 🏷️ اسم اللوحة
            ->brandName($brandName)

            // 🖼️ اللوجو الخفيف (light) من الـ theme
            ->brandLogo(fn () => $theme?->logo_light ? asset($theme->logo_light) : null)

            // 🎨 كل الألوان الأساسية متأثرة بالـ theme
            ->colors([
                // اللون الأساسي لكل الأزرار / الـ primary actions
                'primary'   => Color::hex($primaryHex),

                // تقدر تستخدمه في مكونات مخصصة أو في بعض الأماكن المدعومة في Filament
                'secondary' => Color::hex($secondaryHex),

                // نخلي accent هو اللون اللي نستخدمه للـ info + success (علشان تحس إن السيستم كله ماشي على نفس اللون)
                'accent'    => Color::hex($accentHex),

                // ألوان الحالات
                'info'      => Color::hex($accentHex),
                'success'   => Color::hex($accentHex),

                // تقدر تخلي الـ warning / danger ثابتين أو تخليهم برضه من theme لو حابب
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
            ])

            // Widgets
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets',
            )
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ])

            // Auth Middleware
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
