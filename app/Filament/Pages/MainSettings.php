<?php

namespace App\Filament\Pages;

use App\Services\MainCore\CurrencyService;
use App\Services\MainCore\LocaleService;
use App\Services\MainCore\SettingsService;
use App\Services\MainCore\ThemeService;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class MainSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'MainCore';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'System Settings';

    protected static string $view = 'filament.pages.main-settings'; // مش هنستخدمه، هنشتغل بـ form()

    public ?array $data = [];

    public function mount(
        SettingsService $settings,
        LocaleService $locale,
        CurrencyService $currency,
        ThemeService $theme,
    ): void {
        $defaultCurrency = $currency->defaultCurrency();
        $defaultTheme    = $theme->defaultTheme();

        $this->form->fill([
            'app_name'         => $settings->get('app.name', config('app.name')),
            'app_url'          => $settings->get('app.url', config('app.url')),
            'default_language' => $settings->get('app.default_language', $locale->currentLanguageCode()),
            'default_currency' => $settings->get('app.default_currency', $defaultCurrency?->code),
            'timezone'         => $settings->get('app.timezone', config('app.timezone')),

            'primary_color'    => $defaultTheme?->primary_color,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('app_name')
                                    ->label('Application Name')
                                    ->required(),

                                Forms\Components\TextInput::make('app_url')
                                    ->label('Application URL')
                                    ->url(),
                            ]),

                        Tabs\Tab::make('Localization')
                            ->schema([
                                Forms\Components\Select::make('default_language')
                                    ->label('Default Language')
                                    ->options(
                                        \App\Models\MainCore\Language::where('is_active', true)
                                            ->pluck('name', 'code')
                                    )
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('default_currency')
                                    ->label('Default Currency')
                                    ->options(
                                        \App\Models\MainCore\Currency::where('is_active', true)
                                            ->pluck('code', 'code')
                                    )
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('timezone')
                                    ->label('Timezone')
                                    ->options(
                                        collect(timezone_identifiers_list())
                                            ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                    )
                                    ->searchable()
                                    ->required(),
                            ]),

                        Tabs\Tab::make('Appearance')
                            ->schema([
                                Forms\Components\ColorPicker::make('primary_color')
                                    ->label('Primary Color'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(
        SettingsService $settings,
        ThemeService $themeService,
    ): void {
        $data = $this->form->getState();

        // حفظ settings
        $settings->set('app.name', $data['app_name'] ?? null, group: 'app');
        $settings->set('app.url', $data['app_url'] ?? null, group: 'app');
        $settings->set('app.default_language', $data['default_language'] ?? null, group: 'app');
        $settings->set('app.default_currency', $data['default_currency'] ?? null, group: 'app');
        $settings->set('app.timezone', $data['timezone'] ?? null, group: 'app');

        // تحديث الـ theme الافتراضي (لو موجود)
        if (! empty($data['primary_color'])) {
            $theme = $themeService->defaultTheme();
            if ($theme) {
                $theme->primary_color = $data['primary_color'];
                $theme->save();
            }
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('settings.view_any') ?? false;
    }
}
