<?php

namespace App\Filament\Pages\HR;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\Employee;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class MonthlyAttendanceCalendarPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'monthly_attendance_calendar';
    protected static ?int $navigationSort = 20;
    protected static string $view = 'filament.pages.hr.monthly-attendance-calendar';

    public ?int $employee_id = null;
    public ?string $selected_month = null;

    protected $queryString = [
        'employee_id' => ['except' => ''],
        'selected_month' => ['except' => ''],
    ];

    public static function getNavigationLabel(): string
    {
        return 'monthly_attendance_calendar';
    }

    public function getTitle(): string
    {
        return tr('navigation.hr_monthly_attendance_calendar', [], null, 'dashboard') ?: 'Monthly Attendance Calendar';
    }

    public function getHeading(): string
    {
        return tr('navigation.hr_monthly_attendance_calendar', [], null, 'dashboard') ?: 'Monthly Attendance Calendar';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->selected_month = request()->input('selected_month', now()->format('Y-m'));
        $this->employee_id = request()->input('employee_id');
        
        $monthDate = $this->selected_month ? Carbon::parse($this->selected_month . '-01') : now()->startOfMonth();
        
        $this->filterForm->fill([
            'employee_id' => $this->employee_id,
            'selected_month' => $monthDate->format('Y-m-d'),
        ]);
    }


    protected function getForms(): array
    {
        return [
            'filterForm',
        ];
    }

    public function filterForm(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                Select::make('employee_id')
                    ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                    ->options(function () {
                        return Employee::active()
                            ->get()
                            ->mapWithKeys(fn ($employee) => [
                                $employee->id => $employee->employee_number . ' - ' . $employee->full_name
                            ]);
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->employee_id = $state;
                    }),

                DatePicker::make('selected_month')
                    ->label(tr('fields.month', [], null, 'dashboard') ?: 'Month')
                    ->displayFormat('Y-m')
                    ->format('Y-m-d')
                    ->default(now()->startOfMonth()->format('Y-m-d'))
                    ->firstDayOfWeek(1)
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $date = Carbon::parse($state);
                            $this->selected_month = $date->format('Y-m');
                        } else {
                            $this->selected_month = null;
                        }
                    }),
            ])
            ->statePath('filters');
    }

    public function getJsonUrl(): string
    {
        if (!$this->employee_id || !$this->selected_month) {
            return '';
        }

        $date = Carbon::parse($this->selected_month . '-01');
        $year = $date->year;
        $month = $date->month;

        return route('filament.admin.pages.hr.monthly-attendance-calendar.json', [
            'employee_id' => $this->employee_id,
            'year' => $year,
            'month' => $month,
        ]);
    }
}
