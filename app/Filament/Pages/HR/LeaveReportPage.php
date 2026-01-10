<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\LeaveRequest;
use App\Models\HR\Employee;
use App\Models\HR\LeaveType;
use App\Models\HR\Department;
use App\Services\HR\LeaveReportService;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;

class LeaveReportPage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 40;
    protected static string $view = 'filament.pages.hr.leave-report';

    public array $filters = [];

    public static function getNavigationLabel(): string
    {
        return tr('navigation.hr_leave_reports', [], null, 'dashboard') ?: 'Leave Reports';
    }

    public function getTitle(): string
    {
        return tr('pages.hr_leave_reports.title', [], null, 'dashboard') ?: 'Leave Reports';
    }

    public function getHeading(): string
    {
        return tr('pages.hr_leave_reports.heading', [], null, 'dashboard') ?: 'Leave Reports';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr.leave_reports.view') ?? false;
    }

    public function mount(): void
    {
        $this->filters = [
            'year' => now()->year,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => auth()->user()?->can('hr.leave_reports.export') ?? false)
                ->action(function () {
                    // Export functionality will be implemented
                }),
            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->visible(fn () => auth()->user()?->can('hr.leave_reports.export') ?? false)
                ->action(function () {
                    // Export functionality will be implemented
                }),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->visible(fn () => auth()->user()?->can('hr.leave_reports.export') ?? false)
                ->url(fn () => '#')
                ->openUrlInNewTab(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LeaveRequest::query()
                    ->with(['employee', 'leaveType', 'employee.department'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label(tr('tables.hr_leave_reports.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('tables.hr_leave_reports.employee_name', [], null, 'dashboard') ?: 'Employee Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee.department.name')
                    ->label(tr('tables.hr_leave_reports.department', [], null, 'dashboard') ?: 'Department')
                    ->sortable(),

                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label(tr('tables.hr_leave_reports.leave_type', [], null, 'dashboard') ?: 'Leave Type')
                    ->getStateUsing(fn (LeaveRequest $record) => app()->getLocale() === 'ar' ? $record->leaveType->name_ar : $record->leaveType->name_en)
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('tables.hr_leave_reports.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('tables.hr_leave_reports.end_date', [], null, 'dashboard') ?: 'End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_days')
                    ->label(tr('tables.hr_leave_reports.total_days', [], null, 'dashboard') ?: 'Total Days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.hr_leave_reports.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => tr("status.{$state}", [], null, 'dashboard') ?: ucfirst($state))
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label(tr('tables.hr_leave_reports.filters.year', [], null, 'dashboard') ?: 'Year')
                    ->options(function () {
                        $years = [];
                        for ($i = now()->year - 2; $i <= now()->year + 2; $i++) {
                            $years[$i] = $i;
                        }
                        return $years;
                    })
                    ->default(now()->year)
                    ->native(false)
                    ->modifyQueryUsing(function (Builder $query, $state) {
                        if ($state) {
                            $this->filters['year'] = $state;
                            return $query->whereYear('start_date', $state);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('month')
                    ->label(tr('tables.hr_leave_reports.filters.month', [], null, 'dashboard') ?: 'Month')
                    ->options([
                        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                    ])
                    ->native(false)
                    ->modifyQueryUsing(function (Builder $query, $state) {
                        if ($state) {
                            $this->filters['month'] = $state;
                            return $query->whereMonth('start_date', $state);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(tr('tables.hr_leave_reports.filters.employee', [], null, 'dashboard') ?: 'Employee')
                    ->relationship('employee', 'employee_number')
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->employee_number . ' - ' . $record->full_name)
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->modifyQueryUsing(function (Builder $query, $state) {
                        if ($state) {
                            $this->filters['employee_id'] = $state;
                            return $query->where('employee_id', $state);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('department_id')
                    ->label(tr('tables.hr_leave_reports.filters.department', [], null, 'dashboard') ?: 'Department')
                    ->relationship('employee.department', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->modifyQueryUsing(function (Builder $query, $state) {
                        if ($state) {
                            $this->filters['department_id'] = $state;
                            return $query->whereHas('employee', fn ($q) => $q->where('department_id', $state));
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('leave_type_id')
                    ->label(tr('tables.hr_leave_reports.filters.leave_type', [], null, 'dashboard') ?: 'Leave Type')
                    ->relationship('leaveType', 'name_en')
                    ->getOptionLabelFromRecordUsing(fn (LeaveType $record) => app()->getLocale() === 'ar' ? $record->name_ar : $record->name_en)
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->modifyQueryUsing(function (Builder $query, $state) {
                        if ($state) {
                            $this->filters['leave_type_id'] = $state;
                            return $query->where('leave_type_id', $state);
                        }
                        return $query;
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.hr_leave_reports.filters.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('status.pending', [], null, 'dashboard') ?: 'Pending',
                        'approved' => tr('status.approved', [], null, 'dashboard') ?: 'Approved',
                        'rejected' => tr('status.rejected', [], null, 'dashboard') ?: 'Rejected',
                        'cancelled' => tr('status.cancelled', [], null, 'dashboard') ?: 'Cancelled',
                    ])
                    ->native(false)
                    ->modifyQueryUsing(function (Builder $query, $state) {
                        if ($state) {
                            $this->filters['status'] = $state;
                            return $query->where('status', $state);
                        }
                        return $query;
                    }),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('fields.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('fields.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['date_from'])) {
                            $this->filters['date_from'] = $data['date_from'];
                        }
                        if (isset($data['date_to'])) {
                            $this->filters['date_to'] = $data['date_to'];
                        }
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),
            ])
            ->defaultSort('start_date', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('summary')
                    ->label(tr('actions.show_summary', [], null, 'dashboard') ?: 'Show Summary')
                    ->icon('heroicon-o-information-circle')
                    ->modalHeading(tr('pages.hr_leave_reports.summary', [], null, 'dashboard') ?: 'Report Summary')
                    ->modalContent(function () {
                        $service = app(LeaveReportService::class);
                        $reportData = $service->getReportData($this->filters ?? []);
                        $stats = $reportData['statistics'] ?? [];
                        return view('filament.pages.hr.leave-report-summary', ['stats' => $stats]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(tr('actions.close', [], null, 'dashboard') ?: 'Close'),
            ]);
    }
}

