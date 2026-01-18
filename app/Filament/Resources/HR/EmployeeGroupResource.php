<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\EmployeeGroupResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\EmployeeGroup;
use App\Models\HR\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeGroupResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = EmployeeGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 71;
    protected static ?string $navigationTranslationKey = 'navigation.hr_employee_groups';

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

                        Forms\Components\Select::make('default_schedule_id')
                            ->label(tr('fields.default_schedule', [], null, 'dashboard') ?: 'Default Schedule')
                            ->relationship('defaultSchedule', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\Toggle::make('status')
                            ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('fields.members', [], null, 'dashboard') ?: 'Members')
                    ->schema([
                        Forms\Components\Select::make('members')
                            ->label(tr('fields.members', [], null, 'dashboard') ?: 'Members')
                            ->relationship('employees', 'employee_number')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->employee_number . ' - ' . $record->full_name)
                            ->columnSpanFull(),
                    ]),
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

                Tables\Columns\TextColumn::make('members_count')
                    ->label(tr('fields.members_count', [], null, 'dashboard') ?: 'Members')
                    ->counts('members')
                    ->sortable(),

                Tables\Columns\TextColumn::make('defaultSchedule.name')
                    ->label(tr('fields.default_schedule', [], null, 'dashboard') ?: 'Default Schedule')
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
                    ->visible(fn () => auth()->user()?->can('hr_employee_groups.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_groups.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_groups.delete') ?? false),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeGroups::route('/'),
            'create' => Pages\CreateEmployeeGroup::route('/create'),
            'edit' => Pages\EditEmployeeGroup::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_employee_groups.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_employee_groups.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_employee_groups.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_employee_groups.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

