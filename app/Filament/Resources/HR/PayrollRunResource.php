<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\PayrollRunResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\PayrollRun;
use App\Models\HR\PayrollRunItem;
use App\Models\HR\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PayrollRunResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = PayrollRun::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'hr';
    protected static ?int $navigationSort = 60;
    protected static ?string $navigationTranslationKey = 'sidebar.hr.payroll.payroll';
    protected static ?string $navigationTranslationKey = 'sidebar.hr.payroll';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('year')
                            ->label(trans_dash('forms.payroll.year') ?: 'Year')
                            ->options(fn () => array_combine(
                                range(now()->year - 2, now()->year + 1),
                                range(now()->year - 2, now()->year + 1)
                            ))
                            ->default(now()->year)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('month')
                            ->label(trans_dash('forms.payroll.month') ?: 'Month')
                            ->options([
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                            ])
                            ->default(now()->month)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('department_id')
                            ->label(trans_dash('forms.payroll.department') ?: 'Department')
                            ->relationship('department', 'name', fn (Builder $query) => $query->active())
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->native(false),

                        Forms\Components\Checkbox::make('include_attendance_deductions')
                            ->label(trans_dash('forms.payroll.include_attendance_deductions') ?: 'Include Attendance Deductions')
                            ->default(true),

                        Forms\Components\Checkbox::make('include_loan_installments')
                            ->label(trans_dash('forms.payroll.include_loan_installments') ?: 'Include Loan Installments')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['department', 'items.employee']))
            ->columns([
                Tables\Columns\TextColumn::make('period')
                    ->label(trans_dash('tables.hr_payroll.period') ?: 'Period')
                    ->formatStateUsing(fn (PayrollRun $record) => $record->period)
                    ->sortable(['year', 'month']),

                Tables\Columns\TextColumn::make('department.name')
                    ->label(trans_dash('tables.hr_payroll.department') ?: 'Department')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Employees')
                    ->counts('items')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_net_salary')
                    ->label(trans_dash('tables.hr_payroll.net_salary') ?: 'Total Net Salary')
                    ->money('USD')
                    ->formatStateUsing(fn (PayrollRun $record) => $record->total_net_salary ?? 0),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(trans_dash('tables.hr_payroll.status') ?: 'Status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'approved',
                        'success' => 'paid',
                    ])
                    ->formatStateUsing(fn (string $state): string => trans_dash("status.{$state}") ?: ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('generated_at')
                    ->label('Generated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label(trans_dash('tables.hr_payroll.filters.year') ?: 'Year')
                    ->options(fn () => array_combine(
                        range(now()->year - 2, now()->year + 1),
                        range(now()->year - 2, now()->year + 1)
                    )),

                Tables\Filters\SelectFilter::make('month')
                    ->label(trans_dash('tables.hr_payroll.filters.month') ?: 'Month')
                    ->options([
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                    ]),

                Tables\Filters\SelectFilter::make('department_id')
                    ->label(trans_dash('tables.hr_payroll.filters.department') ?: 'Department')
                    ->relationship('department', 'name'),

                Tables\Filters\SelectFilter::make('status')
                    ->label(trans_dash('tables.hr_payroll.status') ?: 'Status')
                    ->options([
                        'draft' => trans_dash('status.draft') ?: 'Draft',
                        'approved' => trans_dash('status.approved') ?: 'Approved',
                        'paid' => trans_dash('status.paid') ?: 'Paid',
                    ]),
            ])
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollRuns::route('/'),
            'create' => Pages\CreatePayrollRun::route('/create'),
            'view' => Pages\ViewPayrollRun::route('/{record}'),
            'print' => Pages\PrintPayrollRun::route('/{record}/print'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('hr_payroll.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_payroll.create') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
