<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\EmployeeCommissionAssignmentResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\EmployeeCommissionAssignment;
use App\Models\HR\Employee;
use App\Models\HR\Commission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class EmployeeCommissionAssignmentResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = EmployeeCommissionAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'تعيينات العمولات';
    protected static ?int $navigationSort = 32;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(tr('fields.commission_assignment.employee_id', [], null, 'dashboard') ?: 'Employee')
                            ->relationship('employee', 'first_name', fn (Builder $query) => $query->active())
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                            ->searchable(['first_name', 'last_name', 'employee_number'])
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('commission_id')
                            ->label(tr('fields.commission_assignment.commission_id', [], null, 'dashboard') ?: 'Commission')
                            ->relationship('commission', 'name_ar', fn (Builder $query) => $query->active())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\Toggle::make('is_active')
                            ->label(tr('fields.commission_assignment.is_active', [], null, 'dashboard') ?: 'Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('tables.hr_employee_commission_assignments.employee', [], null, 'dashboard') ?: 'Employee')
                    ->searchable(['employee.first_name', 'employee.last_name', 'employee.employee_number'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission.name_ar')
                    ->label(tr('tables.hr_employee_commission_assignments.commission', [], null, 'dashboard') ?: 'Commission')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission.commissionType.name_ar')
                    ->label('Type')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.hr_employee_commission_assignments.is_active', [], null, 'dashboard') ?: 'Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(tr('fields.commission_assignment.employee_id', [], null, 'dashboard') ?: 'Employee')
                    ->relationship('employee', 'first_name')
                    ->searchable()
                    ->preload()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name),

                Tables\Filters\SelectFilter::make('commission_id')
                    ->label(tr('fields.commission_assignment.commission_id', [], null, 'dashboard') ?: 'Commission')
                    ->relationship('commission', 'name_ar')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.hr_employee_commission_assignments.is_active', [], null, 'dashboard') ?: 'Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.update') ?? false),
                TableDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
                Tables\Actions\RestoreAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.restore') ?? false),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
                    Tables\Actions\RestoreBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.restore') ?? false),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false),
                ]),
            ])
            ->defaultSort('employee_id');
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
            'index' => Pages\ListEmployeeCommissionAssignments::route('/'),
            'create' => Pages\CreateEmployeeCommissionAssignment::route('/create'),
            'edit' => Pages\EditEmployeeCommissionAssignment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_employee_commission_assignments.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_employee_commission_assignments.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_employee_commission_assignments.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_employee_commission_assignments.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
