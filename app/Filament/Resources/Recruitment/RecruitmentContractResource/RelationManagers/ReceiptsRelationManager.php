<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\RelationManagers;

use App\Models\Recruitment\RecruitmentContractFinanceLink;
use App\Services\Recruitment\RecruitmentContractFinanceGateway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ReceiptsRelationManager extends RelationManager
{
    protected static string $relationship = 'receipts';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return tr('recruitment_contract.tabs.receipts', [], null, 'dashboard') ?: 'سندات القبض';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label(tr('recruitment_contract.fields.amount', [], null, 'dashboard') ?: 'Amount')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->minValue(0.01),

                Forms\Components\TextInput::make('payment_method')
                    ->label(tr('recruitment_contract.fields.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->maxLength(255),

                Forms\Components\Textarea::make('notes')
                    ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('recruitment_contract.fields.amount', [], null, 'dashboard') ?: 'Amount')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeTransaction.payment_method')
                    ->label(tr('recruitment_contract.fields.payment_method', [], null, 'dashboard') ?: 'Payment Method')
                    ->searchable(),

                Tables\Columns\TextColumn::make('financeTransaction.trx_date')
                    ->label(tr('recruitment_contract.fields.date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('financeTransaction.reference_no')
                    ->label(tr('recruitment_contract.fields.reference_no', [], null, 'dashboard') ?: 'Reference No')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        return $data;
                    })
                    ->after(function (array $data, RelationManager $livewire) {
                        $contract = $livewire->ownerRecord;
                        $financeGateway = app(RecruitmentContractFinanceGateway::class);
                        $financeTransactionId = $financeGateway->postReceipt(
                            $contract,
                            $data['amount'],
                            [
                                'payment_method' => $data['payment_method'] ?? null,
                                'note' => $data['notes'] ?? null,
                            ]
                        );
                    })
                    ->visible(fn () => auth()->user()?->can('recruitment_contracts.finance.manage')),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('recruitment_contracts.finance.manage')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('recruitment_contracts.finance.manage')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
