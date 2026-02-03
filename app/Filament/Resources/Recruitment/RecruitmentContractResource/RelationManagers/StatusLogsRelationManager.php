<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\RelationManagers;

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
        return tr('recruitment_contract.tabs.status_logs', [], null, 'dashboard') ?: 'سجل الأحداث';
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
                    ->label(tr('recruitment_contract.fields.old_status', [], null, 'dashboard') ?: 'Old Status')
                    ->formatStateUsing(fn ($state) => $state ? tr("recruitment_contract.status.{$state}", [], null, 'dashboard') : '-'),

                Tables\Columns\TextColumn::make('new_status')
                    ->label(tr('recruitment_contract.fields.new_status', [], null, 'dashboard') ?: 'New Status')
                    ->formatStateUsing(fn ($state) => tr("recruitment_contract.status.{$state}", [], null, 'dashboard')),

                Tables\Columns\TextColumn::make('notes')
                    ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                    ->limit(50),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('recruitment_contract.fields.created_by', [], null, 'dashboard') ?: 'Created By'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'Created At')
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
