<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\DeviceResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\Device;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeviceResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = Device::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?int $navigationSort = 180;
    protected static ?string $navigationTranslationKey = 'navigation.hr_devices';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(tr('fields.name', [], null, 'dashboard') ?: 'Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('type')
                            ->label(tr('fields.type', [], null, 'dashboard') ?: 'Type')
                            ->options([
                                'fingerprint' => tr('fields.fingerprint', [], null, 'dashboard') ?: 'Fingerprint',
                            ])
                            ->default('fingerprint')
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('ip')
                            ->label(tr('fields.ip_address', [], null, 'dashboard') ?: 'IP Address')
                            ->ip()
                            ->nullable(),

                        Forms\Components\TextInput::make('serial_number')
                            ->label(tr('fields.serial_number', [], null, 'dashboard') ?: 'Serial Number')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('api_key')
                            ->label(tr('fields.api_key', [], null, 'dashboard') ?: 'API Key')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->helperText(tr('fields.api_key_helper', [], null, 'dashboard') ?: 'Auto-generated if left empty'),

                        Forms\Components\Toggle::make('status')
                            ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('fields.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(tr('fields.type', [], null, 'dashboard') ?: 'Type')
                    ->badge(),

                Tables\Columns\TextColumn::make('ip')
                    ->label(tr('fields.ip_address', [], null, 'dashboard') ?: 'IP Address')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('serial_number')
                    ->label(tr('fields.serial_number', [], null, 'dashboard') ?: 'Serial Number')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('api_key')
                    ->label(tr('fields.api_key', [], null, 'dashboard') ?: 'API Key')
                    ->limit(20)
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_devices.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_devices.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_devices.delete') ?? false),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDevices::route('/'),
            'create' => Pages\CreateDevice::route('/create'),
            'edit' => Pages\EditDevice::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_devices.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_devices.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_devices.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_devices.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

