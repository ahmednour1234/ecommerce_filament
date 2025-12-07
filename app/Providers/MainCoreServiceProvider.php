<?php

namespace App\Providers;

use App\Services\MainCore\SettingsService;
use App\Services\MainCore\CurrencyService;
use App\Services\MainCore\ThemeService;
use App\Services\MainCore\LocaleService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MainCoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind services as singletons
        $this->app->singleton(SettingsService::class, fn () => new SettingsService());
        $this->app->singleton(CurrencyService::class, fn () => new CurrencyService());
        $this->app->singleton(ThemeService::class, fn () => new ThemeService());
        $this->app->singleton(LocaleService::class, fn () => new LocaleService());
    }

    public function boot(): void
    {
        // مشاركة بيانات عامة مع كل الـ views (Laravel + Livewire + Filament)
        view()->composer('*', function ($view) {
            $settings = app(SettingsService::class);
            $theme    = app(ThemeService::class);
            $locale   = app(LocaleService::class);

            $view->with('mcAppSettings', $settings->all());
            $view->with('mcTheme', $theme->defaultTheme());
            $view->with('mcCurrentLang', $locale->currentLanguageCode());
            $view->with('mcTimezone', $locale->currentTimezone());
            $view->with('sessionNotifications', \App\Services\NotificationService::getAll());
        });

        // Blade directives بسيطة
        Blade::directive('setting', function ($expression) {
            return "<?php echo app(" . SettingsService::class . "::class)->get($expression); ?>";
        });

        Blade::directive('currency', function ($expression) {
            return "<?php echo app(" . CurrencyService::class . "::class)->format(...[$expression]); ?>";
        });
    }
}
