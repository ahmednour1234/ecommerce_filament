<?php

namespace App\Filament\Resources\Housing\Rental;

use App\Filament\Resources\Housing\Rental\RentalHousingDriverResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingDriver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;

class RentalHousingDriverResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = HousingDriver::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'إيواء التأجير';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.rental_housing.drivers';

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

                Forms\Components\TextInput::make('car_type')
                    ->label(tr('forms.housing.driver.car_type', [], null, 'dashboard') ?: 'نوع السيارة')
                    ->maxLength(255),

                Forms\Components\TextInput::make('car_model')
                    ->label(tr('forms.housing.driver.car_model', [], null, 'dashboard') ?: 'موديل السيارة')
                    ->maxLength(255),

                Forms\Components\TextInput::make('plate_number')
                    ->label(tr('forms.housing.driver.plate', [], null, 'dashboard') ?: 'رقم اللوحة')
                    ->maxLength(255),
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
                    ->color(fn ($record) => $record->license_expiry < now() ? 'danger' : ($record->license_expiry < now()->addDays(30) ? 'warning' : 'success')),

                Tables\Columns\TextColumn::make('car_type')
                    ->label(tr('tables.housing.driver.car_type', [], null, 'dashboard') ?: 'نوع السيارة')
                    ->searchable(),

                Tables\Columns\TextColumn::make('plate_number')
                    ->label(tr('tables.housing.driver.plate', [], null, 'dashboard') ?: 'رقم اللوحة')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('license_expiring')
                    ->label(tr('filters.housing.driver.license_expiring', [], null, 'dashboard') ?: 'رخص منتهية/قريبة الانتهاء')
                    ->query(fn ($query) => $query->where('license_expiry', '<=', now()->addDays(30))),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                TableDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRentalHousingDrivers::route('/'),
            'create' => Pages\CreateRentalHousingDriver::route('/create'),
            'edit' => Pages\EditRentalHousingDriver::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.drivers.view_any') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
