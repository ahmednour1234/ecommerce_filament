<?php

namespace App\Filament\Resources\Rental\RentalContractResource\RelationManagers;

use App\Models\Rental\RentalContractPayment;
use App\Services\Rental\FinanceGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return tr('rental.payments.title', [], null, 'dashboard') ?: 'Payments';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label(tr('rental.payments.amount', [], null, 'dashboard') ?: 'Amount')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->minValue(0.01),

                Forms\Components\DateTimePicker::make('paid_at')
                    ->label(tr('rental.payments.paid_at', [], null, 'dashboard') ?: 'Paid At')
                    ->required()
                    ->default(now()),

                Forms\Components\TextInput::make('method')
                    ->label(tr('rental.payments.method', [], null, 'dashboard') ?: 'Payment Method')
                    ->maxLength(255),

                Forms\Components\TextInput::make('reference')
                    ->label(tr('rental.payments.reference', [], null, 'dashboard') ?: 'Reference')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('rental.payments.amount', [], null, 'dashboard') ?: 'Amount')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label(tr('rental.payments.paid_at', [], null, 'dashboard') ?: 'Paid At')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('method')
                    ->label(tr('rental.payments.method', [], null, 'dashboard') ?: 'Method')
                    ->searchable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label(tr('rental.payments.reference', [], null, 'dashboard') ?: 'Reference')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->colors([
                        'success' => 'posted',
                        'warning' => 'pending',
                        'danger' => 'void',
                        'gray' => 'refunded',
                    ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $data['rental_contract_id'] = $livewire->ownerRecord->id;
                        $data['status'] = 'posted';
                        return $data;
                    })
                    ->after(function (RentalContractPayment $payment) {
                        $contract = $payment->contract;
                        $financeGateway = app(FinanceGateway::class);
                        $financeTransactionId = $financeGateway->postIncome(
                            $contract,
                            $payment->amount,
                            [
                                'payment_method' => $payment->method,
                                'note' => "Payment for contract {$contract->contract_no}",
                            ]
                        );
                        if ($financeTransactionId) {
                            $payment->update(['finance_transaction_id' => $financeTransactionId]);
                        }
                    })
                    ->visible(fn () => auth()->user()?->can('rental.payments.create')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
