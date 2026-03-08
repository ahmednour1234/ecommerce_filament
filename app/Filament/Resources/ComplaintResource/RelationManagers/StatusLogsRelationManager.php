<?php

namespace App\Filament\Resources\ComplaintResource\RelationManagers;

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
        return tr('complaint.tabs.status_logs', [], null, 'dashboard') ?: 'سجل الحالات';
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
                    ->label(tr('complaint.fields.old_status', [], null, 'dashboard') ?: 'الحالة السابقة')
                    ->formatStateUsing(fn ($state) => $state ? tr("complaint.status.{$state}", [], null, 'dashboard') : '-'),

                Tables\Columns\TextColumn::make('new_status')
                    ->label(tr('complaint.fields.new_status', [], null, 'dashboard') ?: 'الحالة الجديدة')
                    ->formatStateUsing(fn ($state) => tr("complaint.status.{$state}", [], null, 'dashboard')),

                Tables\Columns\TextColumn::make('status_date')
                    ->label(tr('complaint.fields.status_date', [], null, 'dashboard') ?: 'تاريخ الحالة')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(tr('complaint.fields.notes', [], null, 'dashboard') ?: 'ملاحظات')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('creator.name')
                    ->label(tr('complaint.fields.created_by', [], null, 'dashboard') ?: 'تم الإنشاء بواسطة'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('status_date', 'desc');
    }
}
