<?php

namespace App\Filament\Resources\Housing\Rental;

use App\Filament\Resources\Housing\Rental\RentalBuildingResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;

class RentalBuildingResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = \App\Models\Housing\Building::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'إيواء التأجير';
    protected static ?string $navigationLabel = 'إدارة المباني';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->rental();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('housing_management.buildings_management', [], null, 'dashboard') ?: 'إدارة المباني')
                    ->schema([
                        Forms\Components\Hidden::make('type')
                            ->default('rental'),

                        Forms\Components\TextInput::make('code')
                            ->label(tr('common.code', [], null, 'dashboard') ?: 'الكود')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_ar')
                            ->label(tr('common.name_ar', [], null, 'dashboard') ?: 'الاسم بالعربية')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('name_en')
                            ->label(tr('common.name_en', [], null, 'dashboard') ?: 'الاسم بالإنجليزية')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('common.branch', [], null, 'dashboard') ?: 'الفرع')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('address')
                            ->label(tr('common.address', [], null, 'dashboard') ?: 'العنوان')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('capacity')
                            ->label(tr('housing.buildings.capacity', [], null, 'dashboard') ?: 'السعة')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('available_capacity')
                            ->label(tr('housing.buildings.available_capacity', [], null, 'dashboard') ?: 'السعة المتاحة')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('common.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options([
                                'active' => tr('common.active', [], null, 'dashboard') ?: 'نشط',
                                'inactive' => tr('common.inactive', [], null, 'dashboard') ?: 'غير نشط',
                                'maintenance' => tr('housing.buildings.maintenance', [], null, 'dashboard') ?: 'صيانة',
                            ])
                            ->default('active')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('common.notes', [], null, 'dashboard') ?: 'ملاحظات')
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
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('common.code', [], null, 'dashboard') ?: 'الكود')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_ar')
                    ->label(tr('common.name_ar', [], null, 'dashboard') ?: 'الاسم بالعربية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name_en')
                    ->label(tr('common.name_en', [], null, 'dashboard') ?: 'الاسم بالإنجليزية')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('common.branch', [], null, 'dashboard') ?: 'الفرع')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacity')
                    ->label(tr('housing.buildings.capacity', [], null, 'dashboard') ?: 'السعة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('available_capacity')
                    ->label(tr('housing.buildings.available_capacity', [], null, 'dashboard') ?: 'السعة المتاحة')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('common.status', [], null, 'dashboard') ?: 'الحالة')
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'maintenance' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("housing.buildings.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('common.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'active' => tr('common.active', [], null, 'dashboard') ?: 'نشط',
                        'inactive' => tr('common.inactive', [], null, 'dashboard') ?: 'غير نشط',
                        'maintenance' => tr('housing.buildings.maintenance', [], null, 'dashboard') ?: 'صيانة',
                    ]),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('common.branch', [], null, 'dashboard') ?: 'الفرع')
                    ->relationship('branch', 'name'),
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
            ->defaultSort('code', 'asc');
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
            'index' => Pages\ListRentalBuildings::route('/'),
            'create' => Pages\CreateRentalBuilding::route('/create'),
            'edit' => Pages\EditRentalBuilding::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('housing.buildings.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('housing.buildings.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('housing.buildings.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('housing.buildings.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
