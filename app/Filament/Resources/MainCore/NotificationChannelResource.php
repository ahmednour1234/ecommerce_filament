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

class NotificationChannelResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = NotificationChannel::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationGroup = 'Notifications';
    protected static ?string $navigationTranslationKey = 'menu.notifications.channels';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'push' => 'Push Notification',
                        'slack' => 'Slack',
                        'webhook' => 'Webhook',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\KeyValue::make('config')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->badge()
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'push' => 'Push Notification',
                        'slack' => 'Slack',
                        'webhook' => 'Webhook',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

