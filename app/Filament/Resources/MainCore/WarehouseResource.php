<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\WarehouseResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WarehouseResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Warehouse::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'MainCore';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Warehouse Code')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->helperText('Unique code for the warehouse (e.g., WH-MAIN)'),

                    Forms\Components\TextInput::make('name')
                        ->label('Warehouse Name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('branch_id')
                        ->relationship('branch', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->required(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Contact Information')
                ->schema([
                    Forms\Components\Textarea::make('address')
                        ->rows(2)
                        ->columnSpanFull()
                        ->nullable(),

                    Forms\Components\TextInput::make('phone')
                        ->tel()
                        ->maxLength(50)
                        ->nullable(),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('warehouses.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('warehouses.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('warehouses.delete') ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('warehouses.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('warehouses.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('warehouses.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('warehouses.delete') ?? false;
    }
}

