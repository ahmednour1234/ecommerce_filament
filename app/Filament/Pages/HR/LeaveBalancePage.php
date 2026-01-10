<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\LeaveBalance;
use App\Models\HR\Employee;
use App\Services\HR\LeaveBalanceService;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class LeaveBalancePage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 30;
    protected static string $view = 'filament.pages.hr.leave-balance';

    public ?int $selectedEmployeeId = null;
    public ?int $selectedYear = null;

    public static function getNavigationLabel(): string
    {
        return tr('navigation.hr_leave_balance', [], null, 'dashboard') ?: 'Leave Balance';
    }

    public function getTitle(): string
    {
        return tr('pages.hr_leave_balance.title', [], null, 'dashboard') ?: 'Leave Balance';
    }

    public function getHeading(): string
    {
        return tr('pages.hr_leave_balance.heading', [], null, 'dashboard') ?: 'Leave Balance';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr.leave_balance.view') ?? false;
    }

    public function mount(): void
    {
        $this->selectedYear = now()->year;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('recalculate')
                ->label(tr('actions.recalculate_balances', [], null, 'dashboard') ?: 'Recalculate Balances')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(tr('actions.recalculate_balances', [], null, 'dashboard') ?: 'Recalculate Balances')
                ->modalDescription(tr('messages.recalculate_balances_confirmation', [], null, 'dashboard') ?: 'This will recalculate all leave balances for the selected year. Continue?')
                ->form([
                    Forms\Components\Select::make('year')
                        ->label(tr('fields.year', [], null, 'dashboard') ?: 'Year')
                        ->options(function () {
                            $years = [];
                            for ($i = now()->year - 2; $i <= now()->year + 2; $i++) {
                                $years[$i] = $i;
                            }
                            return $years;
                        })
                        ->default(now()->year)
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    try {
                        $service = app(LeaveBalanceService::class);
                        $service->recalculateForYear($data['year']);
                        $this->notify('success', tr('messages.balances_recalculated', [], null, 'dashboard') ?: 'Balances recalculated successfully.');
                        $this->resetTable();
                    } catch (\Exception $e) {
                        $this->notify('danger', $e->getMessage());
                    }
                })
                ->visible(fn () => auth()->user()?->can('hr.leave_balance.recalculate') ?? false),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => auth()->user()?->can('hr.leave_balance.export') ?? false)
                ->action(function () {
                    // Export functionality will be implemented
                }),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => auth()->user()?->can('hr.leave_balance.export') ?? false)
                ->action(function () {
                    // Export functionality will be implemented
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LeaveBalance::query()
                    ->with(['employee', 'leaveType', 'employee.department'])
                    ->when($this->selectedEmployeeId, fn ($query) => $query->where('employee_id', $this->selectedEmployeeId))
                    ->when($this->selectedYear, fn ($query) => $query->where('year', $this->selectedYear))
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label(tr('tables.hr_leave_balance.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('tables.hr_leave_balance.employee_name', [], null, 'dashboard') ?: 'Employee Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee.department.name')
                    ->label(tr('tables.hr_leave_balance.department', [], null, 'dashboard') ?: 'Department')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label(tr('tables.hr_leave_balance.leave_type', [], null, 'dashboard') ?: 'Leave Type')
                    ->getStateUsing(fn (LeaveBalance $record) => app()->getLocale() === 'ar' ? $record->leaveType->name_ar : $record->leaveType->name_en)
                    ->sortable(),

                Tables\Columns\TextColumn::make('year')
                    ->label(tr('tables.hr_leave_balance.year', [], null, 'dashboard') ?: 'Year')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quota')
                    ->label(tr('tables.hr_leave_balance.quota', [], null, 'dashboard') ?: 'Yearly Quota')
                    ->sortable(),

                Tables\Columns\TextColumn::make('used')
                    ->label(tr('tables.hr_leave_balance.used', [], null, 'dashboard') ?: 'Used')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining')
                    ->label(tr('tables.hr_leave_balance.remaining', [], null, 'dashboard') ?: 'Remaining')
                    ->sortable()
                    ->color(fn ($state) => $state < 5 ? 'danger' : ($state < 10 ? 'warning' : 'success')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(tr('tables.hr_leave_balance.filters.employee', [], null, 'dashboard') ?: 'Employee')
                    ->relationship('employee', 'employee_number')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->employee_number . ' - ' . $record->full_name)
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->selectedEmployeeId = $state),

                Tables\Filters\SelectFilter::make('year')
                    ->label(tr('tables.hr_leave_balance.filters.year', [], null, 'dashboard') ?: 'Year')
                    ->options(function () {
                        $years = [];
                        for ($i = now()->year - 2; $i <= now()->year + 2; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->default(now()->year)
                    ->native(false)
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->selectedYear = $state),
            ])
            ->defaultSort('employee.employee_number');
    }
}

