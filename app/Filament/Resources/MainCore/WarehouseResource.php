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
    protected static ?string $navigationGroup = 'Integrations';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label(tr('forms.warehouses.code.label', [], null, 'dashboard'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->helperText(tr('forms.warehouses.code.helper', [], null, 'dashboard')),

                    Forms\Components\TextInput::make('name')
                        ->label(tr('forms.warehouses.name.label', [], null, 'dashboard'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('branch_id')
                        ->label(tr('forms.warehouses.branch_id.label', [], null, 'dashboard'))
                        ->relationship('branch', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Toggle::make('is_active')
                        ->label(tr('forms.warehouses.is_active.label', [], null, 'dashboard'))
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
                    ->label(tr('tables.warehouses.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.warehouses.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('tables.warehouses.branch', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.warehouses.is_active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label(tr('tables.warehouses.products', [], null, 'dashboard'))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.warehouses.filters.branch', [], null, 'dashboard'))
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.warehouses.filters.is_active', [], null, 'dashboard'))
                    ->placeholder(tr('common.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.warehouses.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.warehouses.filters.inactive_only', [], null, 'dashboard')),
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

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

