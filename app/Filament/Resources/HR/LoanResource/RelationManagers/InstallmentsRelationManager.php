<?php

namespace App\Filament\Resources\HR\LoanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Services\HR\LoanInstallmentService;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    protected static ?string $title = 'Installments';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('installment_no')
            ->columns([
                Tables\Columns\TextColumn::make('installment_no')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label(tr('fields.due_date', [], null, 'dashboard') ?: 'Due Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(tr('fields.amount', [], null, 'dashboard') ?: 'Amount')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => tr("status.{$state}", [], null, 'dashboard') ?: ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label(tr('fields.paid_at', [], null, 'dashboard') ?: 'Paid At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('status.pending', [], null, 'dashboard') ?: 'Pending',
                        'paid' => tr('status.paid', [], null, 'dashboard') ?: 'Paid',
                    ]),

                Tables\Filters\Filter::make('due_date')
                    ->form([
                        Forms\Components\DatePicker::make('due_from')
                            ->label(tr('fields.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('due_to')
                            ->label(tr('fields.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['due_from'],
                                fn ($query, $date) => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['due_to'],
                                fn ($query, $date) => $query->whereDate('due_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_paid')
                    ->label(tr('actions.mark_paid', [], null, 'dashboard') ?: 'Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $service = app(LoanInstallmentService::class);
                        $service->markAsPaid($record);
                    }),
            ])
            ->defaultSort('installment_no', 'asc')
            ->heading(tr('fields.installments', [], null, 'dashboard') ?: 'Installments');
    }
}
