<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\AttendanceDay;
use App\Models\HR\AttendanceLog;
use App\Models\HR\Employee;
use App\Models\HR\EmployeeSchedule;
use App\Services\HR\AttendanceService;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Carbon\Carbon;

class DailyAttendancePage extends Page implements HasTable
{
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'hr';
    protected static ?int $navigationSort = 34;
    protected static ?string $navigationTranslationKey = 'sidebar.hr.attendance.daily_attendance';
    protected static string $view = 'filament.pages.hr.daily-attendance';

    public ?string $selectedDate = null;

    public static function getNavigationLabel(): string
    {
        return tr('navigation.hr_daily_attendance', [], null, 'dashboard') ?: 'Daily Attendance';
    }

    public function getTitle(): string
    {
        return tr('navigation.hr_daily_attendance', [], null, 'dashboard') ?: 'Daily Attendance';
    }

    public function getHeading(): string
    {
        return tr('navigation.hr_daily_attendance', [], null, 'dashboard') ?: 'Daily Attendance';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr_attendance_daily.view') ?? false;
    }

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m-d');
        $this->aggregateAttendanceForDate($this->selectedDate);
    }

    public function updatedSelectedDate($value): void
    {
        if ($value) {
            $this->aggregateAttendanceForDate($value);
        }
    }

    protected function aggregateAttendanceForDate(string $date): void
    {
        $attendanceService = app(AttendanceService::class);
        $attendanceService->aggregateForDate($date);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('aggregate')
                ->label(tr('actions.aggregate_attendance', [], null, 'dashboard') ?: 'Aggregate Attendance')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    $this->aggregateAttendanceForDate($this->selectedDate ?? now()->format('Y-m-d'));
                    \Filament\Notifications\Notification::make()
                        ->title(tr('messages.attendance_aggregated', [], null, 'dashboard') ?: 'Attendance aggregated successfully')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        $selectedDate = $this->selectedDate ?? now()->format('Y-m-d');
        
        // Auto-aggregate attendance for this date
        $this->aggregateAttendanceForDate($selectedDate);
        
        return $table
            ->query(
                AttendanceDay::query()
                    ->whereDate('date', $selectedDate)
                    ->with(['employee.department', 'employee.position', 'employee.attendanceLogs'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label(tr('fields.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('fields.employee_name', [], null, 'dashboard') ?: 'Employee Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee.department.name')
                    ->label(tr('fields.department', [], null, 'dashboard') ?: 'Department')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('first_in')
                    ->label(tr('fields.first_in', [], null, 'dashboard') ?: 'First In')
                    ->dateTime('H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->description(function ($record) {
                        $descriptions = [];
                        
                        // Get expected time from work schedule
                        if ($record->employee) {
                            $employeeSchedule = EmployeeSchedule::where('employee_id', $record->employee_id)
                                ->forDate($record->date)
                                ->latest()
                                ->first();
                            
                            if ($employeeSchedule && $employeeSchedule->schedule) {
                                $startTime = $employeeSchedule->schedule->start_time;
                                $timeStr = is_string($startTime) ? $startTime : $startTime->format('H:i:s');
                                $expectedTime = Carbon::parse($record->date->format('Y-m-d') . ' ' . substr($timeStr, 0, 5));
                                $expectedLabel = tr('fields.expected_time', [], null, 'dashboard') ?: tr('tables.attendance.expected_time', [], null, 'dashboard') ?: 'Expected';
                                $descriptions[] = $expectedLabel . ': ' . $expectedTime->format('H:i');
                            }
                        }
                        
                        // Get all check-in logs for this day
                        if ($record->employee) {
                            $checkInLogs = AttendanceLog::where('employee_id', $record->employee_id)
                                ->whereDate('log_datetime', $record->date)
                                ->checkIn()
                                ->orderBy('log_datetime')
                                ->get();
                            
                            if ($checkInLogs->count() > 0) {
                                $times = $checkInLogs->map(fn($log) => $log->log_datetime->format('H:i'))->join(', ');
                                $logsLabel = tr('fields.logs', [], null, 'dashboard') ?: tr('tables.biometric_attendances.logs', [], null, 'dashboard') ?: 'Logs';
                                $descriptions[] = $logsLabel . ': ' . $times;
                            }
                        }
                        
                        return !empty($descriptions) ? implode(' | ', $descriptions) : null;
                    }),

                Tables\Columns\TextColumn::make('late_minutes')
                    ->label(tr('fields.late_minutes', [], null, 'dashboard') ?: 'Late Minutes')
                    ->formatStateUsing(function ($state, $record) {
                        // Calculate late minutes from actual check-in time vs expected time
                        if ($record->first_in && $record->employee && $record->first_in instanceof \Carbon\Carbon) {
                            $employeeSchedule = EmployeeSchedule::where('employee_id', $record->employee_id)
                                ->forDate($record->date)
                                ->latest()
                                ->first();
                            
                            if ($employeeSchedule && $employeeSchedule->schedule) {
                                $startTime = $employeeSchedule->schedule->start_time;
                                $timeStr = is_string($startTime) ? $startTime : $startTime->format('H:i:s');
                                $expectedTime = Carbon::parse($record->date->format('Y-m-d') . ' ' . substr($timeStr, 0, 5));
                                $actualTime = $record->first_in;
                                
                                $lateMinutes = max(0, $actualTime->diffInMinutes($expectedTime) - ($employeeSchedule->schedule->late_grace_minutes ?? 0));
                                
                                if ($lateMinutes > 0) {
                                    $hours = floor($lateMinutes / 60);
                                    $minutes = $lateMinutes % 60;
                                    if ($hours > 0) {
                                        return $hours . 'h ' . $minutes . 'm';
                                    }
                                    return $lateMinutes . 'm';
                                }
                                return '-';
                            }
                        }
                        
                        // Fallback to stored value
                        if ($state > 0) {
                            $hours = floor($state / 60);
                            $minutes = $state % 60;
                            if ($hours > 0) {
                                return $hours . 'h ' . $minutes . 'm';
                            }
                            return $minutes . 'm';
                        }
                        return '-';
                    })
                    ->badge()
                    ->color(function ($state, $record) {
                        if ($record->first_in && $record->employee && $record->first_in instanceof \Carbon\Carbon) {
                            $employeeSchedule = EmployeeSchedule::where('employee_id', $record->employee_id)
                                ->forDate($record->date)
                                ->latest()
                                ->first();
                            
                            if ($employeeSchedule && $employeeSchedule->schedule) {
                                $startTime = $employeeSchedule->schedule->start_time;
                                $timeStr = is_string($startTime) ? $startTime : $startTime->format('H:i:s');
                                $expectedTime = Carbon::parse($record->date->format('Y-m-d') . ' ' . substr($timeStr, 0, 5));
                                $actualTime = $record->first_in;
                                $lateMinutes = max(0, $actualTime->diffInMinutes($expectedTime) - ($employeeSchedule->schedule->late_grace_minutes ?? 0));
                                return $lateMinutes > 0 ? 'warning' : 'success';
                            }
                        }
                        return $state > 0 ? 'warning' : 'success';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_out')
                    ->label(tr('fields.last_out', [], null, 'dashboard') ?: 'Last Out')
                    ->dateTime('H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->description(function ($record) {
                        $descriptions = [];
                        
                        // Get expected time from work schedule
                        if ($record->employee) {
                            $employeeSchedule = EmployeeSchedule::where('employee_id', $record->employee_id)
                                ->forDate($record->date)
                                ->latest()
                                ->first();
                            
                            if ($employeeSchedule && $employeeSchedule->schedule) {
                                $endTime = $employeeSchedule->schedule->end_time;
                                $timeStr = is_string($endTime) ? $endTime : $endTime->format('H:i:s');
                                $expectedTime = Carbon::parse($record->date->format('Y-m-d') . ' ' . substr($timeStr, 0, 5));
                                $expectedLabel = tr('fields.expected_time', [], null, 'dashboard') ?: tr('tables.attendance.expected_time', [], null, 'dashboard') ?: 'Expected';
                                $descriptions[] = $expectedLabel . ': ' . $expectedTime->format('H:i');
                            }
                        }
                        
                        // Get all check-out logs for this day
                        if ($record->employee) {
                            $checkOutLogs = AttendanceLog::where('employee_id', $record->employee_id)
                                ->whereDate('log_datetime', $record->date)
                                ->checkOut()
                                ->orderBy('log_datetime')
                                ->get();
                            
                            if ($checkOutLogs->count() > 0) {
                                $times = $checkOutLogs->map(fn($log) => $log->log_datetime->format('H:i'))->join(', ');
                                $logsLabel = tr('fields.logs', [], null, 'dashboard') ?: tr('tables.biometric_attendances.logs', [], null, 'dashboard') ?: 'Logs';
                                $descriptions[] = $logsLabel . ': ' . $times;
                            }
                        }
                        
                        return !empty($descriptions) ? implode(' | ', $descriptions) : null;
                    }),

                Tables\Columns\TextColumn::make('worked_minutes')
                    ->label(tr('fields.worked_minutes', [], null, 'dashboard') ?: 'Worked Hours')
                    ->formatStateUsing(function ($state) {
                        if ($state > 0) {
                            $hours = floor($state / 60);
                            $minutes = $state % 60;
                            if ($hours > 0) {
                                return $hours . 'h ' . $minutes . 'm';
                            }
                            return $minutes . 'm';
                        }
                        return '-';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('overtime_minutes')
                    ->label(tr('fields.overtime_minutes', [], null, 'dashboard') ?: 'Overtime')
                    ->formatStateUsing(fn ($state) => $state > 0 ? round($state / 60, 2) . ' h' : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => tr('fields.present', [], null, 'dashboard') ?: 'Present',
                        'absent' => tr('fields.absent', [], null, 'dashboard') ?: 'Absent',
                        'leave' => tr('fields.leave', [], null, 'dashboard') ?: 'Leave',
                        'holiday' => tr('fields.holiday', [], null, 'dashboard') ?: 'Holiday',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'absent' => 'danger',
                        'leave' => 'warning',
                        'holiday' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'present' => tr('fields.present', [], null, 'dashboard') ?: 'Present',
                        'absent' => tr('fields.absent', [], null, 'dashboard') ?: 'Absent',
                        'leave' => tr('fields.leave', [], null, 'dashboard') ?: 'Leave',
                        'holiday' => tr('fields.holiday', [], null, 'dashboard') ?: 'Holiday',
                    ]),
            ])
            ->defaultSort('employee.employee_number');
    }
}

