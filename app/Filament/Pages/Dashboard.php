<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Dashboard\FinanceBranchesComparisonChartWidget;
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

    public ?string $dateRange = 'month';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?int $finance_branch_id = null;
    public ?int $finance_type_id = null;

    public function mount(): void
    {
        $this->dateRange = session()->get('dashboard_date_range', 'month');
        $this->dateFrom = session()->get('dashboard_date_from');
        $this->dateTo = session()->get('dashboard_date_to');
        $this->finance_branch_id = session()->get('dashboard_finance_branch_id');
        $this->finance_type_id = session()->get('dashboard_finance_type_id');

        $this->form->fill([
            'dateRange' => $this->dateRange,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'finance_branch_id' => $this->finance_branch_id,
            'finance_type_id' => $this->finance_type_id,
        ]);
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
                                'month' => 'هذا الشهر',
                                'custom' => 'مخصص',
                            ])
                            ->default('month')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->dateRange = $state;
                                session()->put('dashboard_date_range', $state);
                            }),

                        \Filament\Forms\Components\DatePicker::make('dateFrom')
                            ->label('من تاريخ')
                            ->visible(fn ($get) => $get('dateRange') === 'custom')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->dateFrom = $state;
                                session()->put('dashboard_date_from', $state);
                            }),

                        \Filament\Forms\Components\DatePicker::make('dateTo')
                            ->label('إلى تاريخ')
                            ->visible(fn ($get) => $get('dateRange') === 'custom')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->dateTo = $state;
                                session()->put('dashboard_date_to', $state);
                            }),

                        \Filament\Forms\Components\Select::make('finance_branch_id')
                            ->label('فرع المالية')
                            ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->finance_branch_id = $state;
                                session()->put('dashboard_finance_branch_id', $state);
                            }),

                        \Filament\Forms\Components\Select::make('finance_type_id')
                            ->label('نوع المالية')
                            ->options(fn () => FinanceType::where('is_active', true)->get()->pluck('name_text', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->finance_type_id = $state;
                                session()->put('dashboard_finance_type_id', $state);
                            }),
                    ])
                    ->columns(5)
                    ->collapsible(),
            ])
            ->statePath('data')
            ->live();
    }

    protected function getDateRange(): array
    {
        $dateRange = $this->dateRange ?? 'month';

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
        ];
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.dashboard', 'لوحة التحكم');
    }
}

