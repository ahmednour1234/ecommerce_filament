<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Dashboard\FinanceBranchesComparisonChartWidget;
use App\Filament\Widgets\Dashboard\FinanceBranchesTableWidget;
use App\Filament\Widgets\Dashboard\FinanceStatsWidget;
use App\Filament\Widgets\Dashboard\FinanceTopTypesWidget;
use App\Filament\Widgets\Dashboard\HRStatsWidget;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;

class Dashboard extends BaseDashboard implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public ?array $data = [];

    public ?string $dateRange = 'year';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?int $finance_branch_id = null;
    public ?int $finance_type_id = null;

    public function mount(): void
    {
        parent::mount();

        $this->dateRange = session()->get('dashboard_date_range', 'year');
        $this->dateFrom = session()->get('dashboard_date_from');
        $this->dateTo = session()->get('dashboard_date_to');
        $this->finance_branch_id = session()->get('dashboard_finance_branch_id');
        $this->finance_type_id = session()->get('dashboard_finance_type_id');

        $this->data = [
            'dateRange' => $this->dateRange,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'finance_branch_id' => $this->finance_branch_id,
            'finance_type_id' => $this->finance_type_id,
        ];

        $this->form->fill($this->data);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('الفلاتر')
                    ->schema([
                        \Filament\Forms\Components\Select::make('dateRange')
                            ->label('الفترة الزمنية')
                            ->options([
                                'today' => 'اليوم',
                                'year' => 'هذا العام',
                                'custom' => 'مخصص',
                            ])
                            ->default('year')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->dateRange = $state;
                                session()->put('dashboard_date_range', $state);
                                $this->dispatch('$refresh');
                            }),

                        \Filament\Forms\Components\DatePicker::make('dateFrom')
                            ->label('من تاريخ')
                            ->visible(fn ($get) => $get('dateRange') === 'custom')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->dateFrom = $state;
                                session()->put('dashboard_date_from', $state);
                                $this->dispatch('$refresh');
                            }),

                        \Filament\Forms\Components\DatePicker::make('dateTo')
                            ->label('إلى تاريخ')
                            ->visible(fn ($get) => $get('dateRange') === 'custom')
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->dateTo = $state;
                                session()->put('dashboard_date_to', $state);
                                $this->dispatch('$refresh');
                            }),

                        \Filament\Forms\Components\Select::make('finance_branch_id')
                            ->label('فرع المالية')
                            ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->finance_branch_id = $state;
                                session()->put('dashboard_finance_branch_id', $state);
                                $this->dispatch('$refresh');
                            }),

                        \Filament\Forms\Components\Select::make('finance_type_id')
                            ->label('نوع المالية')
                            ->options(fn () => FinanceType::where('is_active', true)->get()->pluck('name_text', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                $this->finance_type_id = $state;
                                session()->put('dashboard_finance_type_id', $state);
                                $this->dispatch('$refresh');
                            }),
                    ])
                    ->columns(5)
                    ->collapsible()
                    ->collapsed(false),
            ])
            ->statePath('data')
            ->live();
    }

    protected function getDateRange(): array
    {
        $dateRange = $this->dateRange ?? 'year';

        if ($dateRange === 'today') {
            return [now()->startOfDay(), now()->endOfDay()];
        } elseif ($dateRange === 'month') {
            return [now()->startOfMonth(), now()->endOfMonth()];
        } else {
            return [
                $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : now()->startOfMonth(),
                $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : now()->endOfDay()
            ];
        }
    }

    protected function getBranchId(): ?int
    {
        $user = auth()->user();
        return $user->branch_id ?? null;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            FinanceStatsWidget::class,
            HRStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            FinanceTopTypesWidget::class,
            FinanceBranchesComparisonChartWidget::class,
            FinanceBranchesTableWidget::class,
        ];
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.dashboard', 'لوحة التحكم');
    }
}

