<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\HR\WorkPlace;
use App\Services\HR\AttendanceService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MonthlyAttendanceReportPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 78;
    protected static string $view = 'filament.pages.hr.monthly-attendance-report';

    public ?int $year = null;
    public ?int $month = null;
    public ?int $departmentId = null;
    public ?int $workPlaceId = null;

    public Collection $reportData;

    public static function getNavigationLabel(): string
    {
        return tr('navigation.hr_monthly_report', [], null, 'dashboard') ?: 'Monthly Attendance Report';
    }

    public function getTitle(): string
    {
        return tr('navigation.hr_monthly_report', [], null, 'dashboard') ?: 'Monthly Attendance Report';
    }

    public function getHeading(): string
    {
        return tr('navigation.hr_monthly_report', [], null, 'dashboard') ?: 'Monthly Attendance Report';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr_attendance_report_monthly.view') ?? false;
    }

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
        $this->form->fill();
        $this->loadReport();
    }

    protected function getForms(): array
    {
        return [
            'form',
        ];
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('year')
                    ->label(tr('fields.year', [], null, 'dashboard') ?: 'Year')
                    ->options(array_combine(range(now()->year - 5, now()->year + 1), range(now()->year - 5, now()->year + 1)))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadReport()),

                Forms\Components\Select::make('month')
                    ->label(tr('fields.month', [], null, 'dashboard') ?: 'Month')
                    ->options([
                        1 => tr('months.january', [], null, 'dashboard') ?: 'January',
                        2 => tr('months.february', [], null, 'dashboard') ?: 'February',
                        3 => tr('months.march', [], null, 'dashboard') ?: 'March',
                        4 => tr('months.april', [], null, 'dashboard') ?: 'April',
                        5 => tr('months.may', [], null, 'dashboard') ?: 'May',
                        6 => tr('months.june', [], null, 'dashboard') ?: 'June',
                        7 => tr('months.july', [], null, 'dashboard') ?: 'July',
                        8 => tr('months.august', [], null, 'dashboard') ?: 'August',
                        9 => tr('months.september', [], null, 'dashboard') ?: 'September',
                        10 => tr('months.october', [], null, 'dashboard') ?: 'October',
                        11 => tr('months.november', [], null, 'dashboard') ?: 'November',
                        12 => tr('months.december', [], null, 'dashboard') ?: 'December',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadReport()),

                Forms\Components\Select::make('departmentId')
                    ->label(tr('fields.department', [], null, 'dashboard') ?: 'Department')
                    ->options(Department::active()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadReport()),

                Forms\Components\Select::make('workPlaceId')
                    ->label(tr('fields.work_place', [], null, 'dashboard') ?: 'Work Place')
                    ->options(WorkPlace::active()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadReport()),
            ])
            ->columns(4)
            ->statePath('data');
    }

    public function loadReport(): void
    {
        $service = app(AttendanceService::class);
        $this->reportData = $service->getMonthlyReport(
            $this->year ?? now()->year,
            $this->month ?? now()->month,
            $this->departmentId,
            $this->workPlaceId
        );
    }

    public function applyHolidays(): void
    {
        // This would trigger re-aggregation with holidays
        \Filament\Notifications\Notification::make()
            ->title(tr('messages.holidays_applied', [], null, 'dashboard') ?: 'Holidays applied')
            ->success()
            ->send();
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        // Build union query from collection data
        $unionQuery = null;
        $index = 0;
        foreach ($this->reportData as $row) {
            $rowQuery = DB::query()
                ->selectRaw('? as id, ? as employee_id, ? as employee_number, ? as employee_name, ? as department, ? as position, ? as present_days, ? as absent_days, ? as leave_days, ? as holiday_days, ? as late_days, ? as total_worked_minutes, ? as total_overtime_minutes, ? as total_late_minutes', [
                    $index++,
                    $row['employee_id'] ?? null,
                    $row['employee_number'] ?? '',
                    $row['employee_name'] ?? '',
                    $row['department'] ?? '',
                    $row['position'] ?? '',
                    $row['present_days'] ?? 0,
                    $row['absent_days'] ?? 0,
                    $row['leave_days'] ?? 0,
                    $row['holiday_days'] ?? 0,
                    $row['late_days'] ?? 0,
                    $row['total_worked_minutes'] ?? 0,
                    $row['total_overtime_minutes'] ?? 0,
                    $row['total_late_minutes'] ?? 0,
                ]);
            
            if ($unionQuery === null) {
                $unionQuery = $rowQuery;
            } else {
                $unionQuery->unionAll($rowQuery);
            }
        }
        
        // If no data, create an empty query
        if ($unionQuery === null) {
            $unionQuery = DB::query()
                ->selectRaw('0 as id, null as employee_id, null as employee_number, null as employee_name, null as department, null as position, null as present_days, null as absent_days, null as leave_days, null as holiday_days, null as late_days, null as total_worked_minutes, null as total_overtime_minutes, null as total_late_minutes');
        }
        
        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => Employee::query()
                ->fromSub($unionQuery, 'attendance_report')
                ->select('attendance_report.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label(tr('fields.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee_name')
                    ->label(tr('fields.employee_name', [], null, 'dashboard') ?: 'Employee Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department')
                    ->label(tr('fields.department', [], null, 'dashboard') ?: 'Department')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('position')
                    ->label(tr('fields.position', [], null, 'dashboard') ?: 'Position')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('present_days')
                    ->label(tr('fields.present_days', [], null, 'dashboard') ?: 'Present')
                    ->sortable(),

                Tables\Columns\TextColumn::make('absent_days')
                    ->label(tr('fields.absent_days', [], null, 'dashboard') ?: 'Absent')
                    ->sortable(),

                Tables\Columns\TextColumn::make('leave_days')
                    ->label(tr('fields.leave_days', [], null, 'dashboard') ?: 'Leave')
                    ->sortable(),

                Tables\Columns\TextColumn::make('holiday_days')
                    ->label(tr('fields.holiday_days', [], null, 'dashboard') ?: 'Holidays')
                    ->sortable(),

                Tables\Columns\TextColumn::make('late_days')
                    ->label(tr('fields.late_days', [], null, 'dashboard') ?: 'Late Days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_worked_minutes')
                    ->label(tr('fields.total_worked_hours', [], null, 'dashboard') ?: 'Worked Hours')
                    ->formatStateUsing(fn ($state) => round($state / 60, 2) . ' h')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_overtime_minutes')
                    ->label(tr('fields.total_overtime_hours', [], null, 'dashboard') ?: 'Overtime Hours')
                    ->formatStateUsing(fn ($state) => round($state / 60, 2) . ' h')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_late_minutes')
                    ->label(tr('fields.total_late_hours', [], null, 'dashboard') ?: 'Late Hours')
                    ->formatStateUsing(fn ($state) => round($state / 60, 2) . ' h')
                    ->sortable(),
            ])
            ->filters([])
            ->defaultSort('employee_number');
    }
    
    public function getTableRecordKey($record): string
    {
        return (string) ($record->id ?? $record->employee_id ?? uniqid());
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('apply_holidays')
                ->label(tr('actions.apply_holidays', [], null, 'dashboard') ?: 'Apply Holidays')
                ->icon('heroicon-o-calendar')
                ->action('applyHolidays'),
            \Filament\Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray'),
            \Filament\Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down'),
            \Filament\Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer'),
        ];
    }
}

