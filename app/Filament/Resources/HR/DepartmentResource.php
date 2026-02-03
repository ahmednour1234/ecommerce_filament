<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\DepartmentResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'hr';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationTranslationKey = 'sidebar.hr.settings.departments';
    protected static ?string $navigationTranslationKey = 'navigation.hr_departments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(tr('forms.hr_departments.name.label', [], null, 'dashboard') ?: 'Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('active')
                            ->label(tr('forms.hr_departments.active.label', [], null, 'dashboard') ?: 'Active')
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
                    ->label(tr('tables.hr_departments.name', [], null, 'dashboard') ?: 'Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('active')
                    ->label(tr('tables.hr_departments.active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('positions_count')
                    ->label(tr('tables.hr_departments.positions_count', [], null, 'dashboard') ?: 'Positions')
                    ->counts('positions')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_departments.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.hr_departments.updated_at', [], null, 'dashboard') ?: 'Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label(tr('tables.hr_departments.filters.active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_departments.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_departments.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_departments.delete') ?? false),
                ]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_departments.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_departments.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_departments.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_departments.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_departments.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

