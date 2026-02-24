<?php

namespace App\Filament\Pages\HR;

use App\Models\HR\Department;
use App\Models\HR\Employee;
use App\Models\HR\WorkPlace;
use App\Services\HR\EmployeeWorkPlaceService;
use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

class AssignWorkPlacesPage extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'تعيين أماكن العمل';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.hr.assign-work-places';

    public ?int $departmentId = null;
    public Collection $employees;
    public array $assignments = [];

    public static function getNavigationLabel(): string
    {
        return tr('navigation.hr_assign_work_places', [], null, 'dashboard') ?: 'Assign Work Places';
    }

    public function getTitle(): string
    {
        return tr('navigation.hr_assign_work_places', [], null, 'dashboard') ?: 'Assign Work Places';
    }

    public function getHeading(): string
    {
        return tr('navigation.hr_assign_work_places', [], null, 'dashboard') ?: 'Assign Work Places';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('hr_assign_work_places.view') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
        $this->employees = collect();
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
                Forms\Components\Select::make('departmentId')
                    ->label(tr('fields.department', [], null, 'dashboard') ?: 'Department')
                    ->options(Department::active()->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->departmentId = $state;
                        $this->loadEmployees();
                    }),
            ])
            ->statePath('data');
    }

    public function loadEmployees(): void
    {
        if ($this->departmentId) {
            $service = app(EmployeeWorkPlaceService::class);
            $this->employees = $service->getEmployeesByDepartment($this->departmentId);

            // Load existing assignments
            foreach ($this->employees as $employee) {
                $workPlace = $employee->workPlace?->workPlace;
                if ($workPlace) {
                    $this->assignments[$employee->id] = $workPlace->id;
                }
            }
        } else {
            $this->employees = collect();
            $this->assignments = [];
        }
    }

    public function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->query(
                Employee::query()->whereIn('id', $this->employees->pluck('id'))
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label(tr('fields.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label(tr('fields.employee_name', [], null, 'dashboard') ?: 'Employee Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label(tr('fields.department', [], null, 'dashboard') ?: 'Department')
                    ->sortable(),

                Tables\Columns\TextColumn::make('position.title')
                    ->label(tr('fields.position', [], null, 'dashboard') ?: 'Position')
                    ->sortable(),

                Tables\Columns\TextColumn::make('workPlace.workPlace.name')
                    ->label(tr('fields.work_place', [], null, 'dashboard') ?: 'Work Place')
                    ->default('—'),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([])
            ->headerActions([
                Tables\Actions\Action::make('save')
                    ->label(tr('actions.save', [], null, 'dashboard') ?: 'Save')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action('saveAssignments')
                    ->visible(fn () => $this->employees->isNotEmpty()),
            ]);
    }

    public function saveAssignments(): void
    {
        $service = app(EmployeeWorkPlaceService::class);
        $service->bulkAssign($this->assignments);

        $this->loadEmployees();

        \Filament\Notifications\Notification::make()
            ->title(tr('messages.saved_successfully', [], null, 'dashboard') ?: 'Saved successfully')
            ->success()
            ->send();
    }
}

