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
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.assets';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('forms.assets.sections.basic_information', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(tr('forms.assets.code.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('name')
                            ->label(tr('forms.assets.name.label', [], null, 'dashboard'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label(tr('forms.assets.description.label', [], null, 'dashboard'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('account_id')
                            ->label(tr('forms.assets.account_id.label', [], null, 'dashboard'))
                            ->relationship('account', 'name', fn ($query) => 
                                $query->where('type', 'asset')
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('type')
                            ->label(tr('forms.assets.type.label', [], null, 'dashboard'))
                            ->options([
                                'fixed' => tr('forms.assets.type.options.fixed', [], null, 'dashboard'),
                                'intangible' => tr('forms.assets.type.options.intangible', [], null, 'dashboard'),
                                'current' => tr('forms.assets.type.options.current', [], null, 'dashboard'),
                                'investment' => tr('forms.assets.type.options.investment', [], null, 'dashboard'),
                            ])
                            ->required()
                            ->default('fixed'),

                        Forms\Components\Select::make('category')
                            ->label(tr('forms.assets.category.label', [], null, 'dashboard'))
                            ->options([
                                'property' => tr('forms.assets.category.options.property', [], null, 'dashboard'),
                                'equipment' => tr('forms.assets.category.options.equipment', [], null, 'dashboard'),
                                'vehicle' => tr('forms.assets.category.options.vehicle', [], null, 'dashboard'),
                                'furniture' => tr('forms.assets.category.options.furniture', [], null, 'dashboard'),
                                'computer' => tr('forms.assets.category.options.computer', [], null, 'dashboard'),
                                'other' => tr('forms.assets.category.options.other', [], null, 'dashboard'),
                            ])
                            ->required()
                            ->default('other'),

                        Forms\Components\Select::make('status')
                            ->label(tr('forms.assets.status.label', [], null, 'dashboard'))
                            ->options([
                                'active' => tr('forms.assets.status.options.active', [], null, 'dashboard'),
                                'deprecated' => tr('forms.assets.status.options.deprecated', [], null, 'dashboard'),
                                'disposed' => tr('forms.assets.status.options.disposed', [], null, 'dashboard'),
                                'maintenance' => tr('forms.assets.status.options.maintenance', [], null, 'dashboard'),
                            ])
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('forms.assets.sections.financial_information', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\TextInput::make('purchase_cost')
                            ->label(tr('forms.assets.purchase_cost.label', [], null, 'dashboard'))
                            ->required()
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('current_value')
                            ->label(tr('forms.assets.current_value.label', [], null, 'dashboard'))
                            ->numeric()
                            ->prefix('$'),

                        Forms\Components\DatePicker::make('purchase_date')
                            ->label(tr('forms.assets.purchase_date.label', [], null, 'dashboard'))
                            ->nullable(),

                        Forms\Components\TextInput::make('useful_life_years')
                            ->label(tr('forms.assets.useful_life_years.label', [], null, 'dashboard'))
                            ->numeric()
                            ->minValue(0)
                            ->nullable(),

                        Forms\Components\TextInput::make('depreciation_rate')
                            ->label(tr('forms.assets.depreciation_rate.label', [], null, 'dashboard'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('forms.assets.sections.location_details', [], null, 'dashboard'))
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(tr('forms.assets.branch_id.label', [], null, 'dashboard'))
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('cost_center_id')
                            ->label(tr('forms.assets.cost_center_id.label', [], null, 'dashboard'))
                            ->relationship('costCenter', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('location')
                            ->label(tr('forms.assets.location.label', [], null, 'dashboard'))
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('serial_number')
                            ->label(tr('forms.assets.serial_number.label', [], null, 'dashboard'))
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
                    ->label(tr('tables.assets.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.assets.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label(tr('tables.assets.account', [], null, 'dashboard'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(tr('tables.assets.type', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => $state ? tr('forms.assets.type.options.' . $state, [], null, 'dashboard') : '')
                    ->colors([
                        'primary' => 'fixed',
                        'success' => 'current',
                        'warning' => 'intangible',
                        'info' => 'investment',
                    ])
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->label(tr('tables.assets.category', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => $state ? tr('forms.assets.category.options.' . $state, [], null, 'dashboard') : '')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('purchase_cost')
                    ->label(tr('tables.assets.purchase_cost', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_value')
                    ->label(tr('tables.assets.current_value', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('book_value')
                    ->label(tr('tables.assets.book_value', [], null, 'dashboard'))
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->book_value)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('tables.assets.status', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($state) => $state ? tr('forms.assets.status.options.' . $state, [], null, 'dashboard') : '')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'disposed',
                        'warning' => 'maintenance',
                        'gray' => 'deprecated',
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('tables.assets.filters.type', [], null, 'dashboard'))
                    ->options([
                        'fixed' => tr('forms.assets.type.options.fixed', [], null, 'dashboard'),
                        'intangible' => tr('forms.assets.type.options.intangible', [], null, 'dashboard'),
                        'current' => tr('forms.assets.type.options.current', [], null, 'dashboard'),
                        'investment' => tr('forms.assets.type.options.investment', [], null, 'dashboard'),
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->label(tr('tables.assets.filters.category', [], null, 'dashboard'))
                    ->options([
                        'property' => tr('forms.assets.category.options.property', [], null, 'dashboard'),
                        'equipment' => tr('forms.assets.category.options.equipment', [], null, 'dashboard'),
                        'vehicle' => tr('forms.assets.category.options.vehicle', [], null, 'dashboard'),
                        'furniture' => tr('forms.assets.category.options.furniture', [], null, 'dashboard'),
                        'computer' => tr('forms.assets.category.options.computer', [], null, 'dashboard'),
                        'other' => tr('forms.assets.category.options.other', [], null, 'dashboard'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.assets.filters.status', [], null, 'dashboard'))
                    ->options([
                        'active' => tr('forms.assets.status.options.active', [], null, 'dashboard'),
                        'deprecated' => tr('forms.assets.status.options.deprecated', [], null, 'dashboard'),
                        'disposed' => tr('forms.assets.status.options.disposed', [], null, 'dashboard'),
                        'maintenance' => tr('forms.assets.status.options.maintenance', [], null, 'dashboard'),
                    ]),
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.assets.filters.branch', [], null, 'dashboard'))
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
            ->defaultSort('code', 'asc');
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

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
