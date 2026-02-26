<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\PaymentTransactionResource\Pages;
use App\Filament\Resources\MainCore\PaymentTransactionResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\IntegrationsModuleGate;
use App\Models\MainCore\PaymentTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Actions\EditAction;


class PaymentTransactionResource extends Resource
{
    use TranslatableNavigation, IntegrationsModuleGate;
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Integrations';
    protected static ?string $navigationTranslationKey = 'menu.integrations.payments.transactions';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('payable_type')
                    ->label(tr('forms.payment_transactions.payable_type.label', [], null, 'dashboard'))
                    ->options([
                        'order' => tr('forms.payment_transactions.payable_type.options.order', [], null, 'dashboard'),
                        'invoice' => tr('forms.payment_transactions.payable_type.options.invoice', [], null, 'dashboard'),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('payable_id')
                    ->label(tr('forms.payment_transactions.payable_id.label', [], null, 'dashboard'))
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('user_id')
                    ->label(tr('forms.payment_transactions.user_id.label', [], null, 'dashboard'))
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('payment_method_id')
                    ->label(tr('forms.payment_transactions.payment_method_id.label', [], null, 'dashboard'))
                    ->relationship('method', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('provider_id')
                    ->label(tr('forms.payment_transactions.provider_id.label', [], null, 'dashboard'))
                    ->relationship('provider', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('currency_id')
                    ->label(tr('forms.payment_transactions.currency_id.label', [], null, 'dashboard'))
                    ->relationship('currency', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('amount')
                    ->label(tr('forms.payment_transactions.amount.label', [], null, 'dashboard'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('status')
                    ->label(tr('forms.payment_transactions.status.label', [], null, 'dashboard'))
                    ->options([
                        'pending' => tr('forms.payment_transactions.status.options.pending', [], null, 'dashboard'),
                        'processing' => tr('forms.payment_transactions.status.options.processing', [], null, 'dashboard'),
                        'completed' => tr('forms.payment_transactions.status.options.completed', [], null, 'dashboard'),
                        'failed' => tr('forms.payment_transactions.status.options.failed', [], null, 'dashboard'),
                        'cancelled' => tr('forms.payment_transactions.status.options.cancelled', [], null, 'dashboard'),
                        'refunded' => tr('forms.payment_transactions.status.options.refunded', [], null, 'dashboard'),
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\TextInput::make('provider_reference')
                    ->label(tr('forms.payment_transactions.provider_reference.label', [], null, 'dashboard'))
                    ->maxLength(255),
                Forms\Components\KeyValue::make('meta')
                    ->label(tr('forms.payment_transactions.meta.label', [], null, 'dashboard'))
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('paid_at')
                    ->label(tr('forms.payment_transactions.paid_at.label', [], null, 'dashboard')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payable_type')
                    ->label(tr('tables.payment_transactions.payable_type', [], null, 'dashboard'))
                    ->formatStateUsing(fn (string $state): string => tr('forms.payment_transactions.payable_type.options.' . $state, [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payable_id')
                    ->label(tr('tables.payment_transactions.payable_id', [], null, 'dashboard'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(tr('tables.payment_transactions.user', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('method.name')
                    ->label(tr('tables.payment_transactions.method', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label(tr('tables.payment_transactions.provider', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->label(tr('tables.payment_transactions.currency', [], null, 'dashboard'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('tables.payment_transactions.amount', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.payment_transactions.status', [], null, 'dashboard'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => tr('forms.payment_transactions.status.options.' . $state, [], null, 'dashboard'))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'failed' => 'danger',
                        'cancelled' => 'gray',
                        'refunded' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider_reference')
                    ->label(tr('tables.payment_transactions.provider_reference', [], null, 'dashboard'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label(tr('tables.payment_transactions.paid_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.payment_transactions.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.payment_transactions.updated_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.payment_transactions.filters.status', [], null, 'dashboard'))
                    ->options([
                        'pending' => tr('forms.payment_transactions.status.options.pending', [], null, 'dashboard'),
                        'processing' => tr('forms.payment_transactions.status.options.processing', [], null, 'dashboard'),
                        'completed' => tr('forms.payment_transactions.status.options.completed', [], null, 'dashboard'),
                        'failed' => tr('forms.payment_transactions.status.options.failed', [], null, 'dashboard'),
                        'cancelled' => tr('forms.payment_transactions.status.options.cancelled', [], null, 'dashboard'),
                        'refunded' => tr('forms.payment_transactions.status.options.refunded', [], null, 'dashboard'),
                    ]),
                Tables\Filters\SelectFilter::make('payment_method_id')
                    ->label(tr('tables.payment_transactions.filters.method', [], null, 'dashboard'))
                    ->relationship('method', 'name'),
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label(tr('tables.payment_transactions.filters.provider', [], null, 'dashboard'))
                    ->relationship('provider', 'name'),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label(tr('tables.payment_transactions.filters.user', [], null, 'dashboard'))
                    ->relationship('user', 'name'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListPaymentTransactions::route('/'),
            'create' => Pages\CreatePaymentTransaction::route('/create'),
            'edit' => Pages\EditPaymentTransaction::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('payment_transactions.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

