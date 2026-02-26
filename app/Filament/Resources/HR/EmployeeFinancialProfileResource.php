<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\EmployeeFinancialProfileResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\EmployeeFinancialProfile;
use App\Models\HR\Employee;
use App\Models\HR\SalaryComponent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Actions\EditAction;


class EmployeeFinancialProfileResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = EmployeeFinancialProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'البيانات المالية للموظفين';
    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(trans_dash('forms.employee_financial_profiles.employee') ?: 'Employee')
                            ->relationship(
                                'employee',
                                'first_name',
                                fn (Builder $query) => $query->active()->orderBy('employee_number')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->employee_number} - {$record->full_name}")
                            ->searchable(['employee_number', 'first_name', 'last_name'])
                            ->preload()
                            ->required()
                            ->reactive()
                            ->native(false)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $employee = Employee::find($state);
                                    if ($employee && !$employee->financialProfile) {
                                        $set('base_salary', $employee->basic_salary ?? 0);
                                    } elseif ($employee && $employee->financialProfile) {
                                        $profile = $employee->financialProfile;
                                        $set('base_salary', $profile->base_salary);
                                        $set('status', $profile->status);
                                    }
                                }
                            }),

                        Forms\Components\TextInput::make('base_salary')
                            ->label(trans_dash('forms.employee_financial_profiles.base_salary') ?: 'Base Salary')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                            ->step(0.01)
                            ->default(0),

                        Forms\Components\Select::make('currency_id')
                            ->label(trans_dash('forms.employee_financial_profiles.currency') ?: 'Currency')
                            ->relationship('currency', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->native(false),

                        Forms\Components\Select::make('status')
                            ->label(trans_dash('forms.employee_financial_profiles.status') ?: 'Status')
                            ->options([
                                'active' => trans_dash('forms.employee_financial_profiles.status.active') ?: 'Active',
                                'inactive' => trans_dash('forms.employee_financial_profiles.status.inactive') ?: 'Inactive',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('joined_at')
                            ->label(trans_dash('forms.employee_financial_profiles.joined_at') ?: 'Joined Date')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(trans_dash('forms.employee_financial_profiles.earnings') ?: 'Earnings')
                    ->schema([
                        Forms\Components\Repeater::make('salaryItems')
                            ->relationship('salaryItems', fn (Builder $query) => $query->whereHas('component', fn ($q) => $q->where('type', 'earning')))
                            ->schema([
                                Forms\Components\Select::make('component_id')
                                    ->label(trans_dash('forms.employee_financial_profiles.component') ?: 'Component')
                                    ->relationship('component', 'name', fn (Builder $query) => $query->where('type', 'earning')->active())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->native(false),

                                Forms\Components\TextInput::make('amount')
                                    ->label(trans_dash('forms.employee_financial_profiles.amount') ?: 'Amount')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->default(0),

                                Forms\Components\TextInput::make('notes')
                                    ->label(trans_dash('forms.employee_financial_profiles.notes') ?: 'Notes')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => 
                                $state['component_id'] ? SalaryComponent::find($state['component_id'])?->name : 'New Earning'
                            )
                            ->collapsible()
                            ->defaultItems(0),
                    ])
                    ->columnSpan(1),

                Forms\Components\Section::make(trans_dash('forms.employee_financial_profiles.deductions') ?: 'Deductions')
                    ->schema([
                        Forms\Components\Repeater::make('salaryItemsDeductions')
                            ->relationship('salaryItems', fn (Builder $query) => $query->whereHas('component', fn ($q) => $q->where('type', 'deduction')))
                            ->schema([
                                Forms\Components\Select::make('component_id')
                                    ->label(trans_dash('forms.employee_financial_profiles.component') ?: 'Component')
                                    ->relationship('component', 'name', fn (Builder $query) => $query->where('type', 'deduction')->active())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->native(false),

                                Forms\Components\TextInput::make('amount')
                                    ->label(trans_dash('forms.employee_financial_profiles.amount') ?: 'Amount')
                                    ->numeric()
                                    ->required()
                                    ->prefix('$')
                                    ->step(0.01)
                                    ->default(0),

                                Forms\Components\TextInput::make('notes')
                                    ->label(trans_dash('forms.employee_financial_profiles.notes') ?: 'Notes')
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->itemLabel(fn (array $state): ?string => 
                                $state['component_id'] ? SalaryComponent::find($state['component_id'])?->name : 'New Deduction'
                            )
                            ->collapsible()
                            ->defaultItems(0),
                    ])
                    ->columnSpan(1),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['employee.department']))
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label('Employee Number')
                    ->searchable(['employee.employee_number'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label('Employee')
                    ->searchable(['employee.first_name', 'employee.last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.department.name')
                    ->label(trans_dash('tables.employee_financial_profiles.employee') ?: 'Department')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('base_salary')
                    ->label(trans_dash('tables.employee_financial_profiles.base_salary') ?: 'Base Salary')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(trans_dash('tables.employee_financial_profiles.status') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(trans_dash('tables.employee_financial_profiles.status') ?: 'Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employee_financial.update') ?? false),
            ])
            ->defaultSort('employee.employee_number');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeFinancialProfiles::route('/'),
            'create' => Pages\CreateEmployeeFinancialProfile::route('/create'),
            'edit' => Pages\EditEmployeeFinancialProfile::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('hr_employee_financial.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_employee_financial.update') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_employee_financial.update') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
