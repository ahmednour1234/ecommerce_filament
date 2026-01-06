<?php

namespace App\Filament\Pages;

use App\Models\MainCore\UserPreference;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Filament\Pages\TranslatablePage;

class UserProfile extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatablePage;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = null; // Not in sidebar
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'My Profile';
    protected static ?string $navigationLabel = 'My Profile';

    public static function getNavigationLabel(): string
    {
        return tr('pages.user_profile.title', [], null, 'dashboard') ?: 'My Profile';
    }

    public function getTitle(): string
    {
        return tr('pages.user_profile.title', [], null, 'dashboard') ?: 'My Profile';
    }

    protected static string $view = 'filament.pages.user-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $preference = $user->preferences;

        $this->form->fill([
            'language_id' => $preference?->language_id,
            'theme_id' => $preference?->theme_id,
            'timezone' => $preference?->timezone ?? 'UTC',
            'date_format' => $preference?->date_format ?? 'Y-m-d',
            'time_format' => $preference?->time_format ?? 'H:i',
        ]);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        // Common timezones
        $timezones = [
            'UTC' => 'UTC',
            'America/New_York' => 'America/New_York (EST)',
            'America/Chicago' => 'America/Chicago (CST)',
            'America/Denver' => 'America/Denver (MST)',
            'America/Los_Angeles' => 'America/Los_Angeles (PST)',
            'Europe/London' => 'Europe/London (GMT)',
            'Europe/Paris' => 'Europe/Paris (CET)',
            'Asia/Dubai' => 'Asia/Dubai (GST)',
            'Asia/Kolkata' => 'Asia/Kolkata (IST)',
            'Asia/Tokyo' => 'Asia/Tokyo (JST)',
            'Asia/Shanghai' => 'Asia/Shanghai (CST)',
            'Australia/Sydney' => 'Australia/Sydney (AEDT)',
        ];

        // Common date formats
        $dateFormats = [
            'Y-m-d' => 'YYYY-MM-DD (2024-01-15)',
            'd/m/Y' => 'DD/MM/YYYY (15/01/2024)',
            'm/d/Y' => 'MM/DD/YYYY (01/15/2024)',
            'd-m-Y' => 'DD-MM-YYYY (15-01-2024)',
            'Y/m/d' => 'YYYY/MM/DD (2024/01/15)',
            'd M Y' => 'DD MMM YYYY (15 Jan 2024)',
            'D, d M Y' => 'Day, DD MMM YYYY (Mon, 15 Jan 2024)',
        ];

        // Common time formats
        $timeFormats = [
            'H:i' => '24-hour (14:30)',
            'h:i A' => '12-hour with AM/PM (02:30 PM)',
            'H:i:s' => '24-hour with seconds (14:30:45)',
            'h:i:s A' => '12-hour with seconds and AM/PM (02:30:45 PM)',
        ];

        return $form
            ->schema([
                Forms\Components\Section::make(tr('pages.user_profile.sections.language_theme', [], null, 'dashboard') ?: 'Language & Theme')
                    ->description(tr('pages.user_profile.sections.language_theme_description', [], null, 'dashboard') ?: 'Configure your preferred language and visual theme')
                    ->icon('heroicon-o-language')
                    ->schema([
                        Forms\Components\Select::make('language_id')
                            ->label(tr('pages.user_profile.fields.language', [], null, 'dashboard') ?: 'Language')
                            ->options(
                                \App\Models\MainCore\Language::where('is_active', true)
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText(tr('pages.user_profile.helpers.language', [], null, 'dashboard') ?: 'Select your preferred language for the dashboard'),
                        Forms\Components\Select::make('theme_id')
                            ->label(tr('pages.user_profile.fields.theme', [], null, 'dashboard') ?: 'Theme')
                            ->options(
                                \App\Models\MainCore\Theme::pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->helperText(tr('pages.user_profile.helpers.theme', [], null, 'dashboard') ?: 'Choose a visual theme for your dashboard'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('pages.user_profile.sections.date_time_preferences', [], null, 'dashboard') ?: 'Date & Time Preferences')
                    ->description(tr('pages.user_profile.sections.date_time_preferences_description', [], null, 'dashboard') ?: 'Customize how dates and times are displayed')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\Select::make('timezone')
                            ->label(tr('pages.user_profile.fields.timezone', [], null, 'dashboard') ?: 'Timezone')
                            ->options($timezones)
                            ->searchable()
                            ->default('UTC')
                            ->required()
                            ->helperText(tr('pages.user_profile.helpers.timezone', [], null, 'dashboard') ?: 'Select your timezone'),
                        Forms\Components\Select::make('date_format')
                            ->label(tr('pages.user_profile.fields.date_format', [], null, 'dashboard') ?: 'Date Format')
                            ->options($dateFormats)
                            ->default('Y-m-d')
                            ->required()
                            ->helperText(tr('pages.user_profile.helpers.date_format', [], null, 'dashboard') ?: 'Choose how dates are displayed'),
                        Forms\Components\Select::make('time_format')
                            ->label(tr('pages.user_profile.fields.time_format', [], null, 'dashboard') ?: 'Time Format')
                            ->options($timeFormats)
                            ->default('H:i')
                            ->required()
                            ->helperText(tr('pages.user_profile.helpers.time_format', [], null, 'dashboard') ?: 'Choose how times are displayed'),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $preference = UserPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'language_id' => $data['language_id'],
                'theme_id' => $data['theme_id'] ?? null,
                'timezone' => $data['timezone'] ?? 'UTC',
                'date_format' => $data['date_format'] ?? 'Y-m-d',
                'time_format' => $data['time_format'] ?? 'H:i',
            ]
        );

        // Update session locale if language changed
        if ($preference->language) {
            $languageCode = $preference->language->code;
            session(['locale' => $languageCode]);
            app()->setLocale($languageCode);
        }

        // Add session notification
        \App\Services\NotificationService::add(
            'messages.updated',
            'messages.updated',
            'success'
        );

        $translationService = app(\App\Services\MainCore\TranslationService::class);
        Notification::make()
            ->title($translationService->get('messages.updated', null, 'dashboard', 'Preferences saved successfully'))
            ->success()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Not in sidebar, will be in navbar user menu
    }

}

