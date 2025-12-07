<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Resources\Accounting\AssetResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Accounting\Asset;
use App\Models\Accounting\Account;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('asset_number')
                            ->label('Asset Number')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('name')
                            ->label('Asset Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('account_id')
                            ->label('Asset Account')
                            ->relationship('account', 'name', fn ($query) => 
                                $query->where('type', 'asset')
                            )
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('category')
                            ->label('Category')
                            ->options([
                                'fixed' => 'Fixed Asset',
                                'intangible' => 'Intangible Asset',
                                'current' => 'Current Asset',
                                'investment' => 'Investment',
                            ])
                            ->required()
                            ->default('fixed'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'disposed' => 'Disposed',
                                'sold' => 'Sold',
                                'damaged' => 'Damaged',
                            ])
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Financial Information')
                    ->schema([
                        Forms\Components\TextInput::make('purchase_cost')
                            ->label('Purchase Cost')
                            ->required()
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('current_value')
                            ->label('Current Value')
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Purchase Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\IntegerInput::make('useful_life_years')
                            ->label('Useful Life (Years)')
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('depreciation_rate')
                            ->label('Depreciation Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Location & Details')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('Branch')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('cost_center_id')
                            ->label('Cost Center')
                            ->relationship('costCenter', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('location')
                            ->label('Location')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('warranty_expiry_date')
                            ->label('Warranty Expiry Date'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_number')
                    ->label('Asset #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label('Account')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Category')
                    ->colors([
                        'primary' => 'fixed',
                        'success' => 'current',
                        'warning' => 'intangible',
                        'info' => 'investment',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchase_cost')
                    ->label('Purchase Cost')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_value')
                    ->label('Current Value')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('book_value')
                    ->label('Book Value')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->book_value)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'disposed',
                        'warning' => 'damaged',
                        'gray' => 'sold',
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category'),
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('Branch')
                    ->relationship('branch', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('assets.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('assets.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('assets.delete') ?? false),
                ]),
            ])
            ->defaultSort('asset_number', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('assets.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('assets.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('assets.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('assets.delete') ?? false;
    }
}
