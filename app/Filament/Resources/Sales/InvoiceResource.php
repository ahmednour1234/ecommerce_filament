<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\InvoiceResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\SalesModuleGate;
use App\Models\Sales\Invoice;
use App\Models\Sales\Order;
use App\Models\Sales\Customer;
use App\Models\MainCore\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    use TranslatableNavigation, SalesModuleGate;

    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationTranslationKey = 'menu.sales.invoices';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(trans_dash('forms.invoices.sections.invoice_information'))
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(trans_dash('forms.invoices.invoice_number.label'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'INV-' . strtoupper(uniqid())),

                        Forms\Components\DatePicker::make('invoice_date')
                            ->label(trans_dash('forms.invoices.invoice_date.label'))
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('order_id')
                            ->label(trans_dash('forms.invoices.order_id.label'))
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $order = Order::find($state);
                                    if ($order) {
                                        $set('customer_id', $order->customer_id);
                                        $set('currency_id', $order->currency_id);
                                        $set('subtotal', $order->subtotal);
                                        $set('tax_amount', $order->tax_amount);
                                        $set('discount_amount', $order->discount_amount);
                                        $set('total', $order->total);
                                    }
                                }
                            }),

                        Forms\Components\Select::make('customer_id')
                            ->label(trans_dash('forms.invoices.customer_id.label'))
                            ->relationship('customer', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('status')
                            ->label(trans_dash('forms.invoices.status.label'))
                            ->options([
                                'draft' => trans_dash('forms.invoices.status.options.draft'),
                                'sent' => trans_dash('forms.invoices.status.options.sent'),
                                'paid' => trans_dash('forms.invoices.status.options.paid'),
                                'partial' => trans_dash('forms.invoices.status.options.partial'),
                                'overdue' => trans_dash('forms.invoices.status.options.overdue'),
                                'cancelled' => trans_dash('forms.invoices.status.options.cancelled'),
                            ])
                            ->required()
                            ->default('draft'),

                        Forms\Components\DatePicker::make('due_date')
                            ->label(trans_dash('forms.invoices.due_date.label'))
                            ->required()
                            ->default(now()->addDays(30)),

                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label(trans_dash('forms.invoices.paid_at.label'))
                            ->nullable()
                            ->visible(fn ($get) => in_array($get('status'), ['paid', 'partial'])),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(trans_dash('forms.invoices.sections.financial_information'))
                    ->schema([
                        Forms\Components\Select::make('currency_id')
                            ->label(trans_dash('forms.invoices.currency_id.label'))
                            ->relationship('currency', 'name')
                            ->options(Currency::active()->pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('subtotal')
                            ->label(trans_dash('forms.invoices.subtotal.label'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->prefix('$'),

                        Forms\Components\TextInput::make('tax_amount')
                            ->label(trans_dash('forms.invoices.tax_amount.label'))
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('discount_amount')
                            ->label(trans_dash('forms.invoices.discount_amount.label'))
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\TextInput::make('total')
                            ->label(trans_dash('forms.invoices.total.label'))
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->prefix('$')
                            ->disabled(),
                    ])
                    ->columns(4),

                Forms\Components\Section::make(trans_dash('forms.invoices.sections.invoice_items'))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label(trans_dash('forms.invoices.items.product_id.label'))
                                    ->relationship('product', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Textarea::make('description')
                                    ->label(trans_dash('forms.invoices.items.description.label'))
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('quantity')
                                    ->label(trans_dash('forms.invoices.items.quantity.label'))
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1),

                                Forms\Components\TextInput::make('unit_price')
                                    ->label(trans_dash('forms.invoices.items.unit_price.label'))
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->prefix('$'),

                                Forms\Components\TextInput::make('discount')
                                    ->label(trans_dash('forms.invoices.items.discount.label'))
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('$'),

                                Forms\Components\TextInput::make('total')
                                    ->label(trans_dash('forms.invoices.items.total.label'))
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(trans_dash('tables.invoices.invoice_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice_date')
                    ->label(trans_dash('tables.invoices.date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(trans_dash('tables.invoices.customer'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label(trans_dash('tables.invoices.order'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(trans_dash('tables.invoices.status'))
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'sent',
                        'success' => 'paid',
                        'warning' => 'partial',
                        'danger' => 'overdue',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(trans_dash('tables.invoices.total'))
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label(trans_dash('tables.invoices.due_date'))
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->due_date && $record->due_date->isPast() && $record->status !== 'paid' ? 'danger' : null),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label(trans_dash('tables.invoices.paid_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(trans_dash('filters.invoices.status.label'))
                    ->options([
                        'draft' => trans_dash('filters.invoices.status.options.draft'),
                        'sent' => trans_dash('filters.invoices.status.options.sent'),
                        'paid' => trans_dash('filters.invoices.status.options.paid'),
                        'partial' => trans_dash('filters.invoices.status.options.partial'),
                        'overdue' => trans_dash('filters.invoices.status.options.overdue'),
                        'cancelled' => trans_dash('filters.invoices.status.options.cancelled'),
                    ]),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(trans_dash('filters.invoices.customer_id.label'))
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('overdue')
                    ->label(trans_dash('filters.invoices.overdue.label'))
                    ->query(fn ($query) => $query->where('due_date', '<', now())
                        ->whereNotIn('status', ['paid', 'cancelled'])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('invoices.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('invoices.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('invoices.delete') ?? false),
                ]),
            ])
            ->defaultSort('invoice_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('invoices.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('invoices.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('invoices.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('invoices.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

