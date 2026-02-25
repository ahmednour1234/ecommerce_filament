<?php

namespace App\Filament\Pages\Housing\Rental;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Laborer;
use App\Models\Housing\HousingSalaryDeduction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class RentalHousingSalaryPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'إيواء التأجير';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.rental_housing.laborer_salaries';
    protected static string $view = 'filament.pages.housing.create-salary';

    public ?int $laborer_id = null;
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
        return tr('sidebar.housing.rental_housing.laborer_salaries', [], null, 'dashboard') ?: 'راتب العمالة';
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
        return auth()->user()?->can('housing.salaries.create') ?? false;
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
                        \Filament\Forms\Components\Select::make('laborer_id')
                            ->label(tr('housing.salary.laborer', [], null, 'dashboard') ?: 'اسم العامل')
                            ->options(function () {
                                return Laborer::query()
                                    ->whereHas('housingAssignments', fn ($q) => $q->whereNull('end_date'))
                                    ->get()
                                    ->mapWithKeys(fn ($laborer) => [
                                        $laborer->id => $laborer->name_ar
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state) {
                                    $this->laborer_id = $state;
                                    $laborer = Laborer::find($state);
                                    if ($laborer && $laborer->monthly_salary_amount) {
                                        $set('basic_salary', $laborer->monthly_salary_amount);
                                        $this->basic_salary = $laborer->monthly_salary_amount;
                                    }
                                    // Calculate deductions if month is set
                                    $month = $get('month');
                                    if ($month) {
                                        $this->month = $month;
                                        $this->calculateDeductions($month);
                                        $set('deductions', $this->deductions);
                                        $set('net_salary', $this->net_salary);
                                    } else {
                                        $this->calculateNetSalary();
                                        $set('net_salary', $this->net_salary);
                                    }
                                }
                            })
                            ->columnSpan(1),

                        \Filament\Forms\Components\DatePicker::make('month')
                            ->label(tr('housing.salary.month', [], null, 'dashboard') ?: 'الشهر')
                            ->required()
                            ->displayFormat('F Y')
                            ->format('Y-m')
                            ->native(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $laborerId = $get('laborer_id');
                                if ($state && $laborerId) {
                                    $this->laborer_id = $laborerId;
                                    $this->month = $state;
                                    $this->calculateDeductions($state);
                                    $set('deductions', $this->deductions);
                                    $set('net_salary', $this->net_salary);
                                }
                            })
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
                            ->helperText(tr('housing.salary.deductions_helper', [], null, 'dashboard') ?: 'سيتم حسابها تلقائياً من الخصومات المحددة')
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateNetSalary())
                            ->disabled(fn ($get) => !empty($get('laborer_id')) && !empty($get('month')))
                            ->dehydrated()
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

    public function calculateDeductions(?string $month = null): void
    {
        if (!$this->laborer_id || !$month) {
            return;
        }

        $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $month);
        
        $totalDeductions = HousingSalaryDeduction::where('laborer_id', $this->laborer_id)
            ->where('status', 'applied')
            ->whereMonth('deduction_date', $monthDate->month)
            ->whereYear('deduction_date', $monthDate->year)
            ->sum('amount');

        $this->deductions = (float) $totalDeductions;
        $this->calculateNetSalary();
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
        
        // Calculate deductions if not already calculated
        if (empty($data['deductions']) && !empty($data['laborer_id']) && !empty($data['month'])) {
            $this->calculateDeductions($data['month']);
            $data['deductions'] = $this->deductions;
        }
        
        $data['net_salary'] = $this->net_salary;
        $data['type'] = 'rental';

        \App\Models\Housing\HousingSalary::create([
            'laborer_id' => $data['laborer_id'],
            'type' => $data['type'],
            'month' => $data['month'],
            'basic_salary' => $data['basic_salary'],
            'overtime_hours' => $data['overtime_hours'] ?? 0,
            'overtime_amount' => $data['overtime_amount'] ?? 0,
            'bonuses' => $data['bonuses'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
            'net_salary' => $data['net_salary'],
            'notes' => $data['notes'] ?? null,
        ]);

        Notification::make()
            ->title(tr('messages.saved_successfully', [], null, 'dashboard') ?: 'تم الحفظ بنجاح')
            ->success()
            ->send();

        $this->form->fill();
        $this->reset(['net_salary', 'deductions']);
    }
}
