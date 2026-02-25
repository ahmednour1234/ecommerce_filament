<?php

namespace App\Filament\Pages\Messaging;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\SmsSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SmsSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'قسم الرسائل';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'القوالب والإعدادات';
    protected static string $view = 'filament.pages.messaging.sms-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'current_balance' => SmsSetting::getValue('current_balance', '0'),
            'sender_name' => SmsSetting::getValue('sender_name', ''),
            'daily_limit' => SmsSetting::getValue('daily_limit', '500'),
            'is_sending_enabled' => SmsSetting::getValue('is_sending_enabled', 'true') === 'true',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('إعدادات الرسائل')
                    ->schema([
                        Forms\Components\TextInput::make('current_balance')
                            ->label(tr('forms.sms_settings.current_balance', [], null, 'dashboard') ?: 'الرصيد الحالي')
                            ->numeric()
                            ->default('0')
                            ->required(),

                        Forms\Components\TextInput::make('sender_name')
                            ->label(tr('forms.sms_settings.sender_name', [], null, 'dashboard') ?: 'اسم المرسل')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('daily_limit')
                            ->label(tr('forms.sms_settings.daily_limit', [], null, 'dashboard') ?: 'الحد اليومي')
                            ->numeric()
                            ->default(500)
                            ->required()
                            ->minValue(1),

                        Forms\Components\Toggle::make('is_sending_enabled')
                            ->label(tr('forms.sms_settings.is_sending_enabled', [], null, 'dashboard') ?: 'تفعيل الإرسال')
                            ->default(true),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        SmsSetting::setValue('current_balance', $data['current_balance']);
        SmsSetting::setValue('sender_name', $data['sender_name']);
        SmsSetting::setValue('daily_limit', $data['daily_limit']);
        SmsSetting::setValue('is_sending_enabled', $data['is_sending_enabled'] ? 'true' : 'false');

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('manage_sms_settings') ?? false;
    }
}
