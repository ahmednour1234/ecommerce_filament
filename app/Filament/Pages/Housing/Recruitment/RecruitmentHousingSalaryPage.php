<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\Employee;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class RecruitmentHousingSalaryPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'recruitment_housing';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_housing.laborer_salaries';
    protected static string $view = 'filament.pages.housing.create-salary';

    public ?int $employee_id = null;
    public ?string $month = null;
    public ?float $basic_salary = null;
    public ?int $overtime_hours = 0;
    public ?float $overtime_amount = 0.0;
    public ?float $bonuses = 0.0;
    public ?float $deductions = 0.0;
    public ?float $net_salary = 0.0;
    public ?string $notes = null;

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.recruitment_housing.laborer_salaries', [], null, 'dashboard') ?: 'رواتب العمالة';
    }

    public function getTitle(): string
    {
        return tr('housing.salary.create', [], null, 'dashboard') ?: 'إضافة راتب للعامل';
    }

    public function getHeading(): string
    {
        return tr('housing.salary.create', [], null, 'dashboard') ?: 'إضافة راتب للعامل';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getForms(): array
    {
        return ['form'];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make(tr('housing.salary.create', [], null, 'dashboard') ?: 'إضافة راتب للعامل')
                    ->schema([
                        \Filament\Forms\Components\Select::make('employee_id')
                            ->label(tr('housing.salary.employee', [], null, 'dashboard') ?: 'اسم العامل')
                            ->options(function () {
                                return Employee::query()
                                    ->get()
                                    ->mapWithKeys(fn ($employee) => [
                                        $employee->id => $employee->first_name . ' ' . $employee->last_name
                                    ])
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        \Filament\Forms\Components\DatePicker::make('month')
                            ->label(tr('housing.salary.month', [], null, 'dashboard') ?: 'الشهر')
                            ->required()
                            ->displayFormat('Y-m')
                            ->format('Y-m')
                            ->native(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('basic_salary')
                            ->label(tr('housing.salary.basic_salary', [], null, 'dashboard') ?: 'الراتب الأساسي')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateNetSalary())
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('overtime_hours')
                            ->label(tr('housing.salary.overtime_hours', [], null, 'dashboard') ?: 'ساعات العمل الإضافي')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateNetSalary())
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('overtime_amount')
                            ->label(tr('housing.salary.overtime_amount', [], null, 'dashboard') ?: 'مبلغ العمل الإضافي')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateNetSalary())
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('bonuses')
                            ->label(tr('housing.salary.bonuses', [], null, 'dashboard') ?: 'المكافآت')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateNetSalary())
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('deductions')
                            ->label(tr('housing.salary.deductions', [], null, 'dashboard') ?: 'إجمالي الخصومات')
                            ->numeric()
                            ->default(0)
                            ->helperText(tr('housing.salary.deductions', [], null, 'dashboard') ?: 'إجمالي الخصومات')
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateNetSalary())
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('net_salary')
                            ->label(tr('housing.salary.net_salary', [], null, 'dashboard') ?: 'الراتب الصافي')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0)
                            ->columnSpan(1),

                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label(tr('housing.salary.notes', [], null, 'dashboard') ?: 'ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function calculateNetSalary(): void
    {
        $basic = (float) ($this->basic_salary ?? 0);
        $overtime = (float) ($this->overtime_amount ?? 0);
        $bonuses = (float) ($this->bonuses ?? 0);
        $deductions = (float) ($this->deductions ?? 0);

        $this->net_salary = $basic + $overtime + $bonuses - $deductions;
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['net_salary'] = $this->net_salary;
        $data['type'] = 'recruitment';

        \App\Models\Housing\HousingSalary::create($data);

        Notification::make()
            ->title(tr('messages.saved_successfully', [], null, 'dashboard') ?: 'تم الحفظ بنجاح')
            ->success()
            ->send();

        $this->form->fill();
        $this->reset(['net_salary']);
    }
}
