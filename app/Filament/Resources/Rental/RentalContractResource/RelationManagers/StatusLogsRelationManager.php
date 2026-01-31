<?php

namespace App\Filament\Resources\Rental\RentalContractResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class StatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    protected static ?string $title = null;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return tr('rental.status_logs.title', [], null, 'dashboard') ?: 'Status Logs';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('new_status')
            ->columns([
                Tables\Columns\TextColumn::make('old_status')
                    ->label('Old Status')
                    ->formatStateUsing(fn ($state) => $state ? tr("rental.status.{$state}", [], null, 'dashboard') : '-'),

                Tables\Columns\TextColumn::make('new_status')
                    ->label('New Status')
                    ->formatStateUsing(fn ($state) => tr("rental.status.{$state}", [], null, 'dashboard')),

                Tables\Columns\TextColumn::make('note')
                    ->label('Note')
                    ->limit(50),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Created By'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
    }
}
