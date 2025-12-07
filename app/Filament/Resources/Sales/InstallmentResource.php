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

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->prefix('$'),

                        Forms\Components\DatePicker::make('due_date')
                            ->label('Due Date')
                            ->required()
                            ->default(now()->addDays(30)),

                        Forms\Components\DatePicker::make('paid_date')
                            ->label('Paid Date')
                            ->nullable()
                            ->visible(fn ($get) => in_array($get('status'), ['paid'])),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending')
                            ->reactive(),

                        Forms\Components\Select::make('payment_method_id')
                            ->label('Payment Method')
                            ->relationship('paymentMethod', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Payment Reference')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull()
                            ->nullable(),
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

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),

                Tables\Columns\TextColumn::make('paid_date')
                    ->label('Paid Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Remaining')
                    ->money('USD')
                    ->getStateUsing(fn ($record) => $record->remaining_amount)
                    ->color('danger')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Payment Method')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_reference')
                    ->label('Reference')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'gray' => 'cancelled',
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Installments')
                    ->query(fn ($query) => $query->where('status', 'pending')
                        ->where('due_date', '<', now())),
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

