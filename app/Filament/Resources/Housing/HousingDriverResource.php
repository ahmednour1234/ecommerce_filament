<?php

namespace App\Filament\Resources\Housing;

use App\Filament\Resources\Housing\HousingDriverResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingDriver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class HousingDriverResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = HousingDriver::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'إدارة السائقين';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.drivers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(tr('forms.housing.driver.name', [], null, 'dashboard') ?: 'الاسم')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('phone')
                    ->label(tr('forms.housing.driver.phone', [], null, 'dashboard') ?: 'الجوال')
                    ->tel()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50),

                Forms\Components\TextInput::make('identity_number')
                    ->label(tr('forms.housing.driver.identity', [], null, 'dashboard') ?: 'رقم الهوية')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\TextInput::make('license_number')
                    ->label(tr('forms.housing.driver.license', [], null, 'dashboard') ?: 'رقم الرخصة')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\DatePicker::make('license_expiry')
                    ->label(tr('forms.housing.driver.license_expiry', [], null, 'dashboard') ?: 'انتهاء الرخصة')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.housing.driver.name', [], null, 'dashboard') ?: 'الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(tr('tables.housing.driver.phone', [], null, 'dashboard') ?: 'الجوال')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_number')
                    ->label(tr('tables.housing.driver.license', [], null, 'dashboard') ?: 'رقم الرخصة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('license_expiry')
                    ->label(tr('tables.housing.driver.license_expiry', [], null, 'dashboard') ?: 'انتهاء الرخصة')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->license_expiry < now() ? 'danger' : ($record->license_expiry < now()->addDays(30) ? 'warning' : null)),

                Tables\Columns\TextColumn::make('cars_count')
                    ->label('عدد السيارات')
                    ->counts('cars')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                TableDeleteAction::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHousingDrivers::route('/'),
            'create' => Pages\CreateHousingDriver::route('/create'),
            'edit' => Pages\EditHousingDriver::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.drivers.view_any') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
