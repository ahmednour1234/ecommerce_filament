<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\RelationManagers;

use App\Models\ServiceTransfer;
use App\Models\ServiceTransferPayment;
use App\Models\MainCore\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'المدفوعات';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('payment_method_id')
                    ->label('طريقة الدفع')
                    ->options(function () {
                        return PaymentMethod::where('is_active', true)
                            ->get()
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->minValue(0.01),

                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payment_no')
            ->columns([
                Tables\Columns\TextColumn::make('payment_no')
                    ->label('رقم الدفعة')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('طريقة الدفع')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('أنشئ بواسطة')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $data['transfer_id'] = $livewire->ownerRecord->id;
                        return $data;
                    })
                    ->after(function (ServiceTransferPayment $payment) {
                        $transfer = $payment->transfer;
                        if ($transfer) {
                            ServiceTransfer::recalculatePaymentStatus($transfer);
                        }
                    })
                    ->visible(fn () => auth()->user()?->can('service_transfer.payments.create') ?? false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (ServiceTransferPayment $payment) {
                        $transfer = $payment->transfer;
                        if ($transfer) {
                            ServiceTransfer::recalculatePaymentStatus($transfer);
                        }
                    })
                    ->visible(fn () => auth()->user()?->can('service_transfer.payments.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function () {
                            $transfer = $this->ownerRecord;
                            if ($transfer) {
                                ServiceTransfer::recalculatePaymentStatus($transfer);
                            }
                        }),
                ]),
            ]);
    }
}
