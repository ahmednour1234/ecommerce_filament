<?php

namespace App\Filament\Resources\Biometric;

use App\Filament\Resources\Biometric\BiometricAttendanceResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Biometric\BiometricAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BiometricAttendanceResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = BiometricAttendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?int $navigationSort = 186;
    protected static ?string $navigationTranslationKey = 'navigation.biometric_attendances';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('device_id')
                            ->label(tr('fields.device', [], null, 'dashboard') ?: 'Device')
                            ->relationship('device', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('user_id')
                            ->label(tr('tables.biometric_attendances.user_id', [], null, 'dashboard') ?: 'User ID')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('attended_at')
                            ->label(tr('tables.biometric_attendances.attended_at', [], null, 'dashboard') ?: 'Attended At')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),

                        Forms\Components\TextInput::make('state')
                            ->label(tr('tables.biometric_attendances.state', [], null, 'dashboard') ?: 'State')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\TextInput::make('type')
                            ->label(tr('tables.biometric_attendances.type', [], null, 'dashboard') ?: 'Type')
                            ->numeric()
                            ->nullable(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label(tr('fields.ip_address', [], null, 'dashboard') ?: 'IP Address')
                            ->ip()
                            ->nullable(),

                        Forms\Components\Toggle::make('processed')
                            ->label(tr('tables.biometric_attendances.processed', [], null, 'dashboard') ?: 'Processed')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('device.name')
                    ->label(tr('fields.device', [], null, 'dashboard') ?: 'Device')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user_id')
                    ->label(tr('tables.biometric_attendances.user_id', [], null, 'dashboard') ?: 'User ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('attended_at')
                    ->label(tr('tables.biometric_attendances.attended_at', [], null, 'dashboard') ?: 'Attended At')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('state')
                    ->label(tr('tables.biometric_attendances.state', [], null, 'dashboard') ?: 'State')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(tr('tables.biometric_attendances.type', [], null, 'dashboard') ?: 'Type')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('processed')
                    ->label(tr('tables.biometric_attendances.processed', [], null, 'dashboard') ?: 'Processed')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('device_id')
                    ->label(tr('fields.device', [], null, 'dashboard') ?: 'Device')
                    ->relationship('device', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('processed')
                    ->label(tr('tables.biometric_attendances.processed', [], null, 'dashboard') ?: 'Processed')
                    ->placeholder('All')
                    ->trueLabel('Processed only')
                    ->falseLabel('Unprocessed only'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('attended_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBiometricAttendances::route('/'),
            'view' => Pages\ViewBiometricAttendance::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
