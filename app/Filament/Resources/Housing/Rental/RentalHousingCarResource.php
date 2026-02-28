<?php

namespace App\Filament\Resources\Housing\Rental;

use App\Filament\Resources\Housing\Rental\RentalHousingCarResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Housing\HousingCar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;
use Illuminate\Database\Eloquent\Builder;

class RentalHousingCarResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = HousingCar::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'إدارة السائقين';
    protected static ?string $navigationLabel = 'إدارة السيارات';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->rental();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اولا إدخال البيانات الأساسية')
                    ->schema([
                        Forms\Components\Hidden::make('type')
                            ->default('rental'),

                        Forms\Components\TextInput::make('car_type')
                            ->label('نوع السيارة')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('car_model')
                            ->label('موديل السيارة')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('plate_number')
                            ->label('رقم اللوحة')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('serial_number')
                            ->label('رقم التسلسلي')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('driver_id')
                            ->label('السائق المعين')
                            ->relationship('driver', 'name')
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('insurance_expiry_date')
                            ->label('تاريخ انتهاء التأمين')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('inspection_expiry_date')
                            ->label('تاريخ انتهاء الفحص')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('form_expiry_date')
                            ->label('تاريخ انتهاء الاستمارة')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\FileUpload::make('car_form_file')
                            ->label('إرفاق استمارة السيارة')
                            ->required()
                            ->acceptedFileTypes(['application/pdf', 'image/*'])
                            ->directory('housing/cars/forms')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('driver_notes')
                            ->label('ملاحظات عن السائق')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('car_type')
                    ->label('نوع السيارة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('car_model')
                    ->label('موديل السيارة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('plate_number')
                    ->label('رقم اللوحة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('serial_number')
                    ->label('رقم التسلسلي')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('driver.name')
                    ->label('السائق المعين')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('insurance_expiry_date')
                    ->label('تاريخ انتهاء التأمين')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->insurance_expiry_date < now() ? 'danger' : ($record->insurance_expiry_date < now()->addDays(30) ? 'warning' : null)),

                Tables\Columns\TextColumn::make('inspection_expiry_date')
                    ->label('تاريخ انتهاء الفحص')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->inspection_expiry_date < now() ? 'danger' : ($record->inspection_expiry_date < now()->addDays(30) ? 'warning' : null)),

                Tables\Columns\TextColumn::make('form_expiry_date')
                    ->label('تاريخ انتهاء الاستمارة')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->form_expiry_date < now() ? 'danger' : ($record->form_expiry_date < now()->addDays(30) ? 'warning' : null)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('driver_id')
                    ->label('السائق')
                    ->relationship('driver', 'name')
                    ->searchable(),
            ])
            ->actions([
                EditAction::make(),
                TableDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('plate_number', 'asc');
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
            'index' => Pages\ListRentalHousingCars::route('/'),
            'create' => Pages\CreateRentalHousingCar::route('/create'),
            'edit' => Pages\EditRentalHousingCar::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.cars.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('housing.cars.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('housing.cars.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('housing.cars.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
