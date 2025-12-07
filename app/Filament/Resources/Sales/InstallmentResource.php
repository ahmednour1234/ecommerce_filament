<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\InstallmentResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Sales\Installment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InstallmentResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Installment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Installment Information')
                    ->schema([
                        Forms\Components\TextInput::make('installment_number')
                            ->label('Installment Number')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'INST-' . strtoupper(uniqid())),

                        Forms\Components\Select::make('installmentable_type')
                            ->label('Type')
                            ->options([
                                \App\Models\Sales\Order::class => 'Order',
                                \App\Models\Sales\Invoice::class => 'Invoice',
                            ])
                            ->required()
                            ->reactive(),

                        Forms\Components\Select::make('installmentable_id')
                            ->label('Order/Invoice')
                            ->required()
                            ->searchable()
                            ->options(function ($get) {
                                $type = $get('installmentable_type');
                                if (!$type) {
                                    return [];
                                }
                                
                                if ($type === \App\Models\Sales\Order::class) {
                                    return \App\Models\Sales\Order::pluck('order_number', 'id')->toArray();
                                } else {
                                    return \App\Models\Sales\Invoice::pluck('invoice_number', 'id')->toArray();
                                }
                            })
                            ->preload(),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Start Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Select::make('frequency')
                            ->label('Payment Frequency')
                            ->options([
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'biweekly' => 'Biweekly',
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'yearly' => 'Yearly',
                            ])
                            ->required()
                            ->default('monthly'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'overdue' => 'Overdue',
                            ])
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Payment Details')
                    ->schema([
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\IntegerInput::make('installment_count')
                            ->label('Number of Installments')
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && $get('total_amount')) {
                                    $set('installment_amount', $get('total_amount') / $state);
                                }
                            }),

                        Forms\Components\TextInput::make('installment_amount')
                            ->label('Amount Per Installment')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('$')
                            ->disabled(),

                        Forms\Components\TextInput::make('interest_rate')
                            ->label('Interest Rate (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('installment_number')
                    ->label('Installment #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('installmentable_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('installmentable_id')
                    ->label('Order/Invoice')
                    ->getStateUsing(function ($record) {
                        if (!$record->installmentable) {
                            return '-';
                        }
                        if ($record->installmentable_type === \App\Models\Sales\Order::class) {
                            return $record->installmentable->order_number ?? '-';
                        }
                        return $record->installmentable->invoice_number ?? '-';
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('installment_count')
                    ->label('Count')
                    ->sortable(),

                Tables\Columns\TextColumn::make('installment_amount')
                    ->label('Per Installment')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Paid Amount')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->paid_amount)
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Remaining')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->remaining_amount)
                    ->color('danger')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'primary' => 'completed',
                        'danger' => 'overdue',
                        'gray' => 'cancelled',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('overdue_payments_count')
                    ->label('Overdue')
                    ->getStateUsing(fn ($record) => $record->overdue_payments_count)
                    ->color('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('frequency'),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Installments')
                    ->query(fn ($query) => $query->where('status', 'overdue')
                        ->orWhereHas('payments', fn ($q) => 
                            $q->where('status', 'overdue')
                        )),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('installments.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('installments.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('installments.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstallments::route('/'),
            'create' => Pages\CreateInstallment::route('/create'),
            'edit' => Pages\EditInstallment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('installments.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('installments.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('installments.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('installments.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

