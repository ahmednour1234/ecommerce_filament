<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\WorkPlace;
use App\Models\HR\EmployeeGroup;
use App\Models\HR\Employee;
use App\Models\HR\WorkSchedule;
use App\Services\HR\ScheduleCopyService;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class CopySchedulesPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?int $navigationSort = 320;
    protected static string $view = 'filament.pages.hr.copy-schedules';

    public ?string $sourceType = null;
    public ?int $sourceId = null;
    public array $targetEmployeeIds = [];
    public ?int $scheduleId = null;
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public bool $useSourceSchedule = true;

    public static function getNavigationLabel(): string
    {
        return tr('navigation.hr_copy_schedules', [], null, 'dashboard') ?: 'Copy Schedules';
    }

    public function getTitle(): string
    {
        return tr('navigation.hr_copy_schedules', [], null, 'dashboard') ?: 'Copy Schedules';
    }

    public function getHeading(): string
    {
        return tr('navigation.hr_copy_schedules', [], null, 'dashboard') ?: 'Copy Schedules';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr_schedule_copy.create') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
        $this->dateFrom = now()->format('Y-m-d');
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
                Forms\Components\Section::make(tr('fields.copy_from', [], null, 'dashboard') ?: 'Copy From')
                    ->schema([
                        Forms\Components\Select::make('sourceType')
                            ->label(tr('fields.source_type', [], null, 'dashboard') ?: 'Source Type')
                            ->options([
                                'work_place' => tr('fields.work_place', [], null, 'dashboard') ?: 'Work Place',
                                'employee_group' => tr('fields.employee_group', [], null, 'dashboard') ?: 'Employee Group',
                                'employee' => tr('fields.employee', [], null, 'dashboard') ?: 'Employee',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->sourceType = $state;
                                $this->sourceId = null;
                            }),

                        Forms\Components\Select::make('sourceId')
                            ->label(fn () => match($this->sourceType) {
                                'work_place' => tr('fields.work_place', [], null, 'dashboard') ?: 'Work Place',
                                'employee_group' => tr('fields.employee_group', [], null, 'dashboard') ?: 'Employee Group',
                                'employee' => tr('fields.employee', [], null, 'dashboard') ?: 'Employee',
                                default => tr('fields.source', [], null, 'dashboard') ?: 'Source',
                            })
                            ->options(function () {
                                if ($this->sourceType === 'work_place') {
                                    return WorkPlace::active()->pluck('name', 'id');
                                } elseif ($this->sourceType === 'employee_group') {
                                    return EmployeeGroup::active()->pluck('name', 'id');
                                } elseif ($this->sourceType === 'employee') {
                                    return Employee::active()->get()->mapWithKeys(fn ($e) => [$e->id => $e->employee_number . ' - ' . $e->full_name]);
                                }
                                return [];
                            })
                            ->searchable()
                            ->preload()
                            ->required(fn () => $this->sourceType !== null)
                            ->visible(fn () => $this->sourceType !== null)
                            ->reactive(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('fields.target_employees', [], null, 'dashboard') ?: 'Target Employees')
                    ->schema([
                        Forms\Components\Select::make('targetEmployeeIds')
                            ->label(tr('fields.target_employees', [], null, 'dashboard') ?: 'Target Employees')
                            ->multiple()
                            ->options(Employee::active()->get()->mapWithKeys(fn ($e) => [$e->id => $e->employee_number . ' - ' . $e->full_name]))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),

                Forms\Components\Section::make(tr('fields.schedule_details', [], null, 'dashboard') ?: 'Schedule Details')
                    ->schema([
                        Forms\Components\Toggle::make('useSourceSchedule')
                            ->label(tr('fields.use_source_schedule', [], null, 'dashboard') ?: 'Use Source Schedule')
                            ->default(true)
                            ->reactive()
                            ->afterStateUpdated(function ($state) {
                                $this->useSourceSchedule = $state;
                            }),

                        Forms\Components\Select::make('scheduleId')
                            ->label(tr('fields.schedule', [], null, 'dashboard') ?: 'Schedule')
                            ->options(WorkSchedule::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(fn () => !$this->useSourceSchedule)
                            ->visible(fn () => !$this->useSourceSchedule),

                        Forms\Components\DatePicker::make('dateFrom')
                            ->label(tr('fields.date_from', [], null, 'dashboard') ?: 'Date From')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('dateTo')
                            ->label(tr('fields.date_to', [], null, 'dashboard') ?: 'Date To')
                            ->nullable()
                            ->after('dateFrom'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function copySchedules(): void
    {
        $data = $this->form->getState();
        
        $service = app(ScheduleCopyService::class);
        
        try {
            if ($data['sourceType'] === 'work_place') {
                $service->copyFromWorkPlace(
                    $data['sourceId'],
                    $data['targetEmployeeIds'],
                    $data['dateFrom'],
                    $data['dateTo'] ?? null
                );
            } elseif ($data['sourceType'] === 'employee_group') {
                $service->copyFromGroup(
                    $data['sourceId'],
                    $data['targetEmployeeIds'],
                    $data['dateFrom'],
                    $data['dateTo'] ?? null
                );
            } elseif ($data['sourceType'] === 'employee') {
                $service->copyFromEmployee(
                    $data['sourceId'],
                    $data['targetEmployeeIds'],
                    $data['dateFrom'],
                    $data['dateTo'] ?? null
                );
            }

            \Filament\Notifications\Notification::make()
                ->title(tr('messages.schedules_copied_successfully', [], null, 'dashboard') ?: 'Schedules copied successfully')
                ->success()
                ->send();

            $this->form->fill();
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title(tr('messages.error', [], null, 'dashboard') ?: 'Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}

