<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\AttendanceDay;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Pages\Page;

class DailyAttendancePage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'HR > Attendance';
    protected static ?int $navigationSort = 8;
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
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(
                AttendanceDay::query()
                    ->whereDate('date', $this->selectedDate ?? now())
                    ->with(['employee.department', 'employee.position'])
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
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_out')
                    ->label(tr('fields.last_out', [], null, 'dashboard') ?: 'Last Out')
                    ->dateTime('H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('worked_minutes')
                    ->label(tr('fields.worked_minutes', [], null, 'dashboard') ?: 'Worked')
                    ->formatStateUsing(fn ($state) => round($state / 60, 2) . ' h')
                    ->sortable(),

                Tables\Columns\TextColumn::make('late_minutes')
                    ->label(tr('fields.late_minutes', [], null, 'dashboard') ?: 'Late')
                    ->formatStateUsing(fn ($state) => $state > 0 ? round($state / 60, 2) . ' h' : '—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('overtime_minutes')
                    ->label(tr('fields.overtime_minutes', [], null, 'dashboard') ?: 'Overtime')
                    ->formatStateUsing(fn ($state) => $state > 0 ? round($state / 60, 2) . ' h' : '—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
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

