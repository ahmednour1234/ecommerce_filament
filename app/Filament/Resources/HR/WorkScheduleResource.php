<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\WorkScheduleResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\WorkSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkScheduleResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = WorkSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'hr';
    protected static ?int $navigationSort = 32;
    protected static ?string $navigationTranslationKey = 'sidebar.hr.attendance.work_schedules';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(tr('fields.name', [], null, 'dashboard') ?: 'Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TimePicker::make('start_time')
                            ->label(tr('fields.start_time', [], null, 'dashboard') ?: 'Start Time')
                            ->required()
                            ->seconds(false),

                        Forms\Components\TimePicker::make('end_time')
                            ->label(tr('fields.end_time', [], null, 'dashboard') ?: 'End Time')
                            ->required()
                            ->seconds(false)
                            ->after('start_time'),

                        Forms\Components\TextInput::make('break_minutes')
                            ->label(tr('fields.break_minutes', [], null, 'dashboard') ?: 'Break Minutes')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix(' min'),

                        Forms\Components\TextInput::make('late_grace_minutes')
                            ->label(tr('fields.late_grace_minutes', [], null, 'dashboard') ?: 'Late Grace Minutes')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix(' min'),

                        Forms\Components\Toggle::make('status')
                            ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('fields.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label(tr('fields.start_time', [], null, 'dashboard') ?: 'Start Time')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label(tr('fields.end_time', [], null, 'dashboard') ?: 'End Time')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('break_minutes')
                    ->label(tr('fields.break_minutes', [], null, 'dashboard') ?: 'Break')
                    ->suffix(' min')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('late_grace_minutes')
                    ->label(tr('fields.late_grace_minutes', [], null, 'dashboard') ?: 'Grace Period')
                    ->suffix(' min')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_work_schedules.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_work_schedules.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_work_schedules.delete') ?? false),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkSchedules::route('/'),
            'create' => Pages\CreateWorkSchedule::route('/create'),
            'edit' => Pages\EditWorkSchedule::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_work_schedules.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_work_schedules.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_work_schedules.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_work_schedules.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

