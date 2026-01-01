<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\OrderResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Sales\Order;
use App\Models\Sales\Customer;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationTranslationKey = 'menu.sales.orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Order Number')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'ORD-' . strtoupper(uniqid())),

                        Forms\Components\DatePicker::make('order_date')
                            ->label('Order Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('customer_id')
                            ->label('Customer')
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $customer = Customer::find($state);
                                    if ($customer && $customer->currency_id) {
                                        $set('currency_id', $customer->currency_id);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'refunded' => 'Refunded',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Financial Information')
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

                        Forms\Components\Select::make('currency_id')
                            ->label('Currency')
                            ->relationship('currency', 'name')
                            ->options(Currency::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Tax Amount')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Discount Amount')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated()
                            ->afterStateHydrated(function ($component, $state, $get) {
                                // Calculate total from items if not set
                                if (!$state && $get('items')) {
                                    $items = $get('items');
                                    $subtotal = collect($items)->sum('total') ?? 0;
                                    $tax = $get('tax_amount') ?? 0;
                                    $discount = $get('discount_amount') ?? 0;
                                    $total = $subtotal + $tax - $discount;
                                    $component->state($total);
                                }
                            }),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Order Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        if ($state) {
                                            $product = \App\Models\Catalog\Product::find($state);
                                            if ($product) {
                                                $set('unit_price', $product->price);
                                                $quantity = $get('quantity') ?? 1;
                                                $unitPrice = $get('unit_price') ?? $product->price;
                                                $discount = $get('discount') ?? 0;
                                                $set('total', ($unitPrice * $quantity) - $discount);
                                            }
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $discount = $get('discount') ?? 0;
                                        $set('total', ($unitPrice * $state) - $discount);
                                    }),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Unit Price')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->prefix('$')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $quantity = $get('quantity') ?? 1;
                                        $discount = $get('discount') ?? 0;
                                        $set('total', ($state * $quantity) - $discount);
                                    }),

                                Forms\Components\TextInput::make('discount')
                                    ->label('Discount')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $quantity = $get('quantity') ?? 1;
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $set('total', ($unitPrice * $quantity) - $state);
                                    }),

                                Forms\Components\TextInput::make('total')
                                    ->label('Total')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$')
                                    ->disabled(),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->itemLabel(fn (array $state): ?string => 
                                $state['product_id'] ? \App\Models\Catalog\Product::find($state['product_id'])?->name : 'New Item'
                            )
                            ->collapsible(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_date')
                    ->label('Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'gray' => 'refunded',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('currency.symbol')
                    ->label('Currency')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('order_date', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('order_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('orders.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('orders.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('orders.delete') ?? false),
                ]),
            ])
            ->defaultSort('order_date', 'desc');
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('orders.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('orders.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('orders.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('orders.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

