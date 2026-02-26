<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\NotificationChannelResource\Pages;
use App\Filament\Resources\MainCore\NotificationChannelResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\NotificationChannel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Concerns\NotificationModuleGate;
use App\Filament\Actions\EditAction;


class NotificationChannelResource extends Resource
{
    use TranslatableNavigation,NotificationModuleGate;
    protected static ?string $model = NotificationChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Notifications';
    protected static ?string $navigationTranslationKey = 'menu.notifications.channels';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label(tr('forms.notification_channels.type.label', [], null, 'dashboard'))
                    ->options([
                        'email' => tr('forms.notification_channels.type.options.email', [], null, 'dashboard'),
                        'sms' => tr('forms.notification_channels.type.options.sms', [], null, 'dashboard'),
                        'push' => tr('forms.notification_channels.type.options.push', [], null, 'dashboard'),
                        'slack' => tr('forms.notification_channels.type.options.slack', [], null, 'dashboard'),
                        'webhook' => tr('forms.notification_channels.type.options.webhook', [], null, 'dashboard'),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(tr('forms.notification_channels.name.label', [], null, 'dashboard'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\KeyValue::make('config')
                    ->label(tr('forms.notification_channels.config.label', [], null, 'dashboard'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label(tr('forms.notification_channels.is_active.label', [], null, 'dashboard'))
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label(tr('tables.notification_channels.type', [], null, 'dashboard'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => tr('forms.notification_channels.type.options.' . $state, [], null, 'dashboard'))
                    ->color(fn (string $state): string => match ($state) {
                        'email' => 'primary',
                        'sms' => 'success',
                        'push' => 'warning',
                        'slack' => 'info',
                        'webhook' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.notification_channels.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.notification_channels.is_active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.notification_channels.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.notification_channels.updated_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('tables.notification_channels.filters.type', [], null, 'dashboard'))
                    ->options([
                        'email' => tr('forms.notification_channels.type.options.email', [], null, 'dashboard'),
                        'sms' => tr('forms.notification_channels.type.options.sms', [], null, 'dashboard'),
                        'push' => tr('forms.notification_channels.type.options.push', [], null, 'dashboard'),
                        'slack' => tr('forms.notification_channels.type.options.slack', [], null, 'dashboard'),
                        'webhook' => tr('forms.notification_channels.type.options.webhook', [], null, 'dashboard'),
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.notification_channels.filters.is_active', [], null, 'dashboard'))
                    ->placeholder(tr('common.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.notification_channels.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.notification_channels.filters.inactive_only', [], null, 'dashboard')),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationChannels::route('/'),
            'create' => Pages\CreateNotificationChannel::route('/create'),
            'edit' => Pages\EditNotificationChannel::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('notification_channels.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

