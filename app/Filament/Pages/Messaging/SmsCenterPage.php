<?php

namespace App\Filament\Pages\Messaging;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Messaging\MessageContact;
use App\Models\Messaging\SmsMessage;
use App\Models\MainCore\SmsSetting;
use App\Services\Messaging\SmsSendService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SmsCenterPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationGroup = 'قسم الرسائل';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'الرسائل النصية';
    protected static string $view = 'filament.pages.messaging.sms-center';

    public ?string $recipients = '';
    public ?string $message = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('recipients')
                    ->label(tr('forms.sms_messages.recipients', [], null, 'dashboard') ?: 'أرقام المستلمين (مثال: 995...,669...)')
                    ->rows(3)
                    ->placeholder('9951234567, 6698765432')
                    ->required()
                    ->live(),

                Forms\Components\Textarea::make('message')
                    ->label(tr('forms.sms_messages.message', [], null, 'dashboard') ?: 'الرسالة')
                    ->required()
                    ->maxLength(1000)
                    ->rows(5)
                    ->live(),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MessageContact::query())
            ->columns([
                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('tables.message_contacts.name_ar', [], null, 'dashboard') ?: 'الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(tr('tables.message_contacts.phone', [], null, 'dashboard') ?: 'الرقم')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('use')
                    ->label(tr('actions.use', [], null, 'dashboard') ?: 'استخدام')
                    ->icon('heroicon-o-plus')
                    ->action(function (MessageContact $record) {
                        $currentRecipients = $this->recipients ?? '';
                        $this->recipients = trim($currentRecipients . ($currentRecipients ? ', ' : '') . $record->phone);
                        $this->form->fill(['recipients' => $this->recipients]);
                        Notification::make()
                            ->title('تمت إضافة الرقم')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('delete_message_contacts') ?? false),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(tr('actions.add', [], null, 'dashboard') ?: 'إضافة')
                    ->model(MessageContact::class)
                    ->form([
                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('forms.message_contacts.name_ar', [], null, 'dashboard') ?: 'الاسم')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label(tr('forms.message_contacts.phone', [], null, 'dashboard') ?: 'الرقم')
                            ->required(),
                    ])
                    ->successNotification(
                        Notification::make()
                            ->title(tr('notifications.contact_created', [], null, 'dashboard') ?: 'تم إنشاء جهة الاتصال بنجاح')
                            ->success()
                    ),
                Tables\Actions\Action::make('import_customers')
                    ->label(tr('actions.import_customers', [], null, 'dashboard') ?: 'إضافة جميع أرقام العملاء')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->requiresConfirmation()
                    ->action(function () {
                        $service = app(SmsSendService::class);
                        $result = $service->importCustomers();
                        
                        Notification::make()
                            ->title("تم استيراد {$result['imported']} جهة اتصال")
                            ->success()
                            ->send();
                        
                        $this->dispatch('$refresh');
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function send(): void
    {
        $data = $this->form->getState();
        
        if (empty($data['recipients']) || empty($data['message'])) {
            Notification::make()
                ->title('يرجى إدخال المستلمين والرسالة')
                ->danger()
                ->send();
            return;
        }

        $service = app(SmsSendService::class);
        $result = $service->send($data['recipients'], $data['message'], auth()->id());

        if ($result['success']) {
            Notification::make()
                ->title($result['message'])
                ->success()
                ->send();
            
            $this->form->fill([
                'recipients' => '',
                'message' => '',
            ]);
            $this->recipients = '';
            $this->message = '';
        } else {
            Notification::make()
                ->title($result['message'])
                ->danger()
                ->send();
        }
    }

    public function getSentCount(): int
    {
        return SmsMessage::count();
    }

    public function getCurrentBalance(): string
    {
        return SmsSetting::getValue('current_balance', '0');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('send_sms') ?? false;
    }
}
