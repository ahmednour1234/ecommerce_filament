<?php

namespace App\Filament\Resources\Messaging;

use App\Filament\Resources\Messaging\SmsMessageResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Messaging\SmsMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class SmsMessageResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = SmsMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'قسم الرسائل';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'الرسائل المرسلة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('message')
                    ->label(tr('forms.sms_messages.message', [], null, 'dashboard') ?: 'الرسالة')
                    ->required()
                    ->maxLength(1000)
                    ->rows(5)
                    ->columnSpanFull(),

                Forms\Components\Select::make('status')
                    ->label(tr('tables.sms_messages.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'queued' => tr('status.queued', [], null, 'dashboard') ?: 'قيد الإرسال',
                        'sent' => tr('status.sent', [], null, 'dashboard') ?: 'تم الإرسال',
                        'failed' => tr('status.failed', [], null, 'dashboard') ?: 'فشل',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(tr('tables.sms_messages.id', [], null, 'dashboard') ?: 'الرقم')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.sms_messages.created_at', [], null, 'dashboard') ?: 'تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(tr('tables.sms_messages.created_by', [], null, 'dashboard') ?: 'المستخدم/المرسل')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('recipients_count')
                    ->label(tr('tables.sms_messages.recipients_count', [], null, 'dashboard') ?: 'عدد المستلمين')
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->label(tr('tables.sms_messages.message', [], null, 'dashboard') ?: 'نص الرسالة')
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.sms_messages.status', [], null, 'dashboard') ?: 'الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'queued' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'queued' => tr('status.queued', [], null, 'dashboard') ?: 'قيد الإرسال',
                        'sent' => tr('status.sent', [], null, 'dashboard') ?: 'تم الإرسال',
                        'failed' => tr('status.failed', [], null, 'dashboard') ?: 'فشل',
                        default => $state,
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.sms_messages.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'queued' => tr('status.queued', [], null, 'dashboard') ?: 'قيد الإرسال',
                        'sent' => tr('status.sent', [], null, 'dashboard') ?: 'تم الإرسال',
                        'failed' => tr('status.failed', [], null, 'dashboard') ?: 'فشل',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('delete_sms_messages') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('delete_sms_messages') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSmsMessages::route('/'),
            'view' => Pages\ViewSmsMessage::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('view_any_sms_messages') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        return auth()->user()?->can('view_sms_messages') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('delete_sms_messages') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
