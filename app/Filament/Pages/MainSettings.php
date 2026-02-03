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
use Filament\Actions\Action;
use App\Filament\Pages\TranslatablePage;

class MainSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use TranslatablePage;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'general_settings';
    protected static ?int $navigationSort = 7;
    protected static ?string $title = 'System Settings';
    protected static ?string $navigationTranslationKey = 'sidebar.general_settings.system_settings';

    protected static string $view = 'filament.pages.main-settings'; // مش هنستخدمه، هنشتغل بـ form()

    public function getTitle(): string
    {
        return tr('pages.settings.system_settings.title', [], null, 'dashboard');
    }

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
                Forms\Components\Section::make(tr('pages.settings.system_settings.sections.general_settings', [], null, 'dashboard'))
                    ->description(tr('pages.settings.system_settings.sections.general_settings.description', [], null, 'dashboard'))
                    ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\TextInput::make('app_name')
                                    ->label(tr('pages.settings.system_settings.fields.app_name', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                                Forms\Components\TextInput::make('app_url')
                                    ->label(tr('pages.settings.system_settings.fields.app_url', [], null, 'dashboard'))
                            ->url()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('pages.settings.system_settings.sections.localization', [], null, 'dashboard'))
                    ->description(tr('pages.settings.system_settings.sections.localization.description', [], null, 'dashboard'))
                    ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Select::make('default_language')
                                    ->label(tr('pages.settings.system_settings.fields.default_language', [], null, 'dashboard'))
                                    ->options(
                                        \App\Models\MainCore\Language::where('is_active', true)
                                            ->pluck('name', 'code')
                                    )
                                    ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                // Update app locale when language changes
                                if ($state) {
                                    app()->setLocale($state);
                                    session(['locale' => $state]);
                                }
                            }),

                                Forms\Components\Select::make('default_currency')
                                    ->label(tr('pages.settings.system_settings.fields.default_currency', [], null, 'dashboard'))
                                    ->options(
                                        \App\Models\MainCore\Currency::where('is_active', true)
                                    ->pluck('name', 'code')
                                    )
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('timezone')
                                    ->label(tr('pages.settings.system_settings.fields.timezone', [], null, 'dashboard'))
                                    ->options(
                                        collect(timezone_identifiers_list())
                                            ->mapWithKeys(fn ($tz) => [$tz => $tz])
                                    )
                                    ->searchable()
                                    ->required(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(tr('pages.settings.system_settings.sections.appearance', [], null, 'dashboard'))
                    ->description(tr('pages.settings.system_settings.sections.appearance.description', [], null, 'dashboard'))
                    ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\ColorPicker::make('primary_color')
                            ->label(tr('pages.settings.system_settings.fields.primary_color', [], null, 'dashboard'))
                            ->helperText(tr('pages.settings.system_settings.fields.primary_color.helper', [], null, 'dashboard')),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(
        SettingsService $settings,
        ThemeService $themeService,
    ): void {
        $data = $this->form->getState();

        // Save settings
        $settings->set('app.name', $data['app_name'] ?? null, group: 'app');
        $settings->set('app.url', $data['app_url'] ?? null, group: 'app');
        $settings->set('app.default_language', $data['default_language'] ?? null, group: 'app');
        $settings->set('app.default_currency', $data['default_currency'] ?? null, group: 'app');
        $settings->set('app.timezone', $data['timezone'] ?? null, group: 'app');

        // Update default theme if primary color is set
        if (! empty($data['primary_color'])) {
            $theme = $themeService->defaultTheme();
            if ($theme) {
                $theme->primary_color = $data['primary_color'];
                $theme->save();
            }
        }

        // Update app locale if language changed
        if (!empty($data['default_language'])) {
            app()->setLocale($data['default_language']);
            session(['locale' => $data['default_language']]);
        }

        // Add session notification
        \App\Services\NotificationService::add(
            'messages.updated',
            'messages.updated',
            'success'
        );

        $translationService = app(\App\Services\MainCore\TranslationService::class);
        Notification::make()
            ->title($translationService->get('messages.updated', null, 'dashboard', 'Settings saved successfully'))
            ->success()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('settings.view_any') ?? false;
    }

}
