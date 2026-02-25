<?php

namespace App\Filament\Resources\HR\CommissionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeCommissionTiersRelationManager extends RelationManager
{
    protected static string $relationship = 'tiers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contracts_from')
                    ->label(tr('fields.commission_tier.contracts_from', [], null, 'dashboard') ?: 'Contracts From')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Forms\Components\TextInput::make('contracts_to')
                    ->label(tr('fields.commission_tier.contracts_to', [], null, 'dashboard') ?: 'Contracts To')
                    ->numeric()
                    ->required()
                    ->minValue(0),

                Forms\Components\TextInput::make('amount_per_contract')
                    ->label(tr('fields.commission_tier.amount_per_contract', [], null, 'dashboard') ?: 'Amount Per Contract')
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->prefix('$'),

                Forms\Components\Toggle::make('is_active')
                    ->label(tr('fields.commission_tier.is_active', [], null, 'dashboard') ?: 'Active')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contracts_from')
            ->columns([
                Tables\Columns\TextColumn::make('contracts_from')
                    ->label(tr('tables.hr_employee_commission_tiers.contracts_from', [], null, 'dashboard') ?: 'From')
                    ->sortable(),

                Tables\Columns\TextColumn::make('contracts_to')
                    ->label(tr('tables.hr_employee_commission_tiers.contracts_to', [], null, 'dashboard') ?: 'To')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount_per_contract')
                    ->label(tr('tables.hr_employee_commission_tiers.amount_per_contract', [], null, 'dashboard') ?: 'Amount Per Contract')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.hr_employee_commission_tiers.is_active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.hr_employee_commission_tiers.is_active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.create') ?? false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.delete') ?? false),
                ]),
            ])
            ->defaultSort('contracts_from');
    }
}
