<?php

namespace App\Filament\Pages;

use App\Models\MainCore\UserPreference;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class UserProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'MainCore';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'My Profile';
    protected static ?string $navigationLabel = 'My Profile';

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

    public function form(Form $form): Form
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
                Forms\Components\Section::make('User Preferences')
                    ->description('Manage your personal preferences and settings')
                    ->schema([
                        Forms\Components\Select::make('language_id')
                            ->label('Language')
                            ->options(
                                \App\Models\MainCore\Language::where('is_active', true)
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('theme_id')
                            ->label('Theme')
                            ->options(
                                \App\Models\MainCore\Theme::pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('timezone')
                            ->label('Timezone')
                            ->options($timezones)
                            ->searchable()
                            ->default('UTC'),
                        Forms\Components\Select::make('date_format')
                            ->label('Date Format')
                            ->options($dateFormats)
                            ->default('Y-m-d'),
                        Forms\Components\Select::make('time_format')
                            ->label('Time Format')
                            ->options($timeFormats)
                            ->default('H:i'),
                    ]),
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
            session(['locale' => $preference->language->code]);
        }

        Notification::make()
            ->title('Preferences saved successfully')
            ->success()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true; // Always visible to authenticated users
    }
}

