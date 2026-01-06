<?php

namespace App\Filament\Resources\Catalog;

use App\Filament\Resources\Catalog\ProductResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Catalog\Product;
use App\Models\Catalog\Category;
use App\Models\Catalog\Brand;
use App\Models\MainCore\Currency;
use App\Models\MainCore\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Products & Inventory';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationTranslationKey = 'menu.products.products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(trans_dash('forms.products.sections.basic_information'))
                    ->schema([
                        Forms\Components\TextInput::make('sku')
                            ->label(trans_dash('forms.products.sku.label'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(trans_dash('forms.products.sku.helper_text')),

                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('forms.products.name.label'))
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

                        Forms\Components\TextInput::make('slug')
                            ->label(trans_dash('forms.products.slug.label'))
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(trans_dash('forms.products.slug.helper_text')),

                        Forms\Components\Select::make('type')
                            ->label(trans_dash('forms.products.type.label'))
                            ->options([
                                'product' => trans_dash('forms.products.type.options.product'),
                                'service' => trans_dash('forms.products.type.options.service'),
                            ])
                            ->required()
                            ->default('product')
                            ->reactive(),

                        Forms\Components\Select::make('category_id')
                            ->label(trans_dash('forms.products.category_id.label'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Select::make('brand_id')
                            ->label(trans_dash('forms.products.brand_id.label'))
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Textarea::make('description')
                            ->label(trans_dash('forms.products.description.label'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(trans_dash('forms.products.sections.pricing_inventory'))
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->label(trans_dash('forms.products.price.label'))
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('cost')
                            ->label(trans_dash('forms.products.cost.label'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('$')
                            ->helperText(trans_dash('forms.products.cost.helper_text')),

                        Forms\Components\Select::make('currency_id')
                            ->label(trans_dash('forms.products.currency_id.label'))
                            ->relationship('currency', 'name')
                            ->options(Currency::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('stock_quantity')
                            ->label(trans_dash('forms.products.stock_quantity.label'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->visible(fn ($get) => $get('type') === 'product'),

                        Forms\Components\Toggle::make('track_inventory')
                            ->label(trans_dash('forms.products.track_inventory.label'))
                            ->default(true)
                            ->visible(fn ($get) => $get('type') === 'product'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans_dash('forms.products.is_active.label'))
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(trans_dash('forms.products.sections.warehouses'))
                    ->schema([
                        Forms\Components\Repeater::make('warehouses')
                            ->relationship('warehouses')
                            ->schema([
                                Forms\Components\Select::make('id')
                                    ->label('Warehouse')
                                    ->options(Warehouse::active()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Forms\Components\TextInput::make('pivot.quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('pivot.min_stock_level')
                                    ->label('Min Stock Level')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('pivot.max_stock_level')
                                    ->label('Max Stock Level')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => Warehouse::find($state['id'] ?? null)?->name ?? null)
                            ->collapsible(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($get) => $get('type') === 'product'),

                Forms\Components\Section::make(trans_dash('forms.products.sections.batches'))
                    ->schema([
                        Forms\Components\Repeater::make('batches')
                            ->relationship('batches')
                            ->schema([
                                Forms\Components\TextInput::make('batch_number')
                                    ->label('Batch Number')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('warehouse_id')
                                    ->label('Warehouse')
                                    ->relationship('warehouse', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Forms\Components\DatePicker::make('manufacturing_date')
                                    ->label('Manufacturing Date'),
                                Forms\Components\DatePicker::make('expiry_date')
                                    ->label('Expiry Date'),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(0),
                                Forms\Components\TextInput::make('cost')
                                    ->label('Cost')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => $state['batch_number'] ?? null)
                            ->collapsible(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn ($get) => $get('type') === 'product'),

                Forms\Components\Section::make(trans_dash('forms.products.sections.additional_information'))
                    ->schema([
                        Forms\Components\KeyValue::make('meta')
                            ->label('Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->helperText('Additional flexible data (optional)'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label(trans_dash('tables.products.sku'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(trans_dash('tables.products.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label(trans_dash('tables.products.type'))
                    ->colors([
                        'primary' => 'product',
                        'success' => 'service',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label(trans_dash('tables.products.category'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label(trans_dash('tables.products.brand'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(trans_dash('tables.products.price'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label(trans_dash('tables.products.stock'))
                    ->sortable()
                    ->visible(fn () => true)
                    ->color(fn ($record) => $record->stock_quantity <= 0 ? 'danger' : 'success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('tables.products.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(trans_dash('filters.products.type.label'))
                    ->options([
                        'product' => trans_dash('filters.products.type.options.product'),
                        'service' => trans_dash('filters.products.type.options.service'),
                    ]),

                Tables\Filters\SelectFilter::make('category_id')
                    ->label(trans_dash('filters.products.category_id.label'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label(trans_dash('filters.products.brand_id.label'))
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('filters.products.is_active.label'))
                    ->placeholder(trans_dash('filters.products.is_active.placeholder'))
                    ->trueLabel(trans_dash('filters.products.is_active.true_label'))
                    ->falseLabel(trans_dash('filters.products.is_active.false_label')),

                Tables\Filters\Filter::make('stock_quantity')
                    ->label(trans_dash('filters.products.stock_quantity.label'))
                    ->form([
                        Forms\Components\TextInput::make('stock_from')
                            ->label(trans_dash('filters.products.stock_from.label'))
                            ->numeric(),
                        Forms\Components\TextInput::make('stock_to')
                            ->label(trans_dash('filters.products.stock_to.label'))
                            ->numeric(),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['stock_from'],
                                fn ($query, $value) => $query->where('stock_quantity', '>=', $value),
                            )
                            ->when(
                                $data['stock_to'],
                                fn ($query, $value) => $query->where('stock_quantity', '<=', $value),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('products.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('products.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('products.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('products.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('products.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('products.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('products.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

