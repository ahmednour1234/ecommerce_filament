<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceBranchesComparisonChartWidget extends ChartWidget implements HasForms
{
    use InteractsWithForms;

    public ?string $dateRange = 'year';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?int $branchId = null;
    public ?int $financeTypeId = null;

    protected static bool $isDiscovered = true;

    protected static ?string $heading = 'مقارنة الفروع المالية';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    public function mount(): void
    {
        $user = Auth::user();

        $this->dateRange = session()->get('dashboard_date_range', 'year');
        $this->dateFrom = session()->get('dashboard_date_from');
        $this->dateTo = session()->get('dashboard_date_to');
        $this->branchId = session()->get('dashboard_finance_branch_id') ?? $user?->branch_id ?? null;
        $this->financeTypeId = session()->get('dashboard_finance_type_id') ?? null;

        $this->form->fill([
            'dateRange' => $this->dateRange,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'branchId' => $this->branchId,
            'financeTypeId' => $this->financeTypeId,
        ]);
    }

    protected function hasForm(): bool
    {
        return true;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('فلاتر مقارنة الفروع')
                ->schema([
                    Select::make('dateRange')
                        ->label('الفترة الزمنية')
                        ->options([
                            'today' => 'اليوم',
                            'year' => 'هذا العام',
                            'custom' => 'مخصص',
                        ])
                        ->default('year')
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->dateRange = $state;
                            session()->put('dashboard_date_range', $state);
                            $this->flushCache();
                            $this->dispatch('$refresh');
                        }),

                    DatePicker::make('dateFrom')
                        ->label('من تاريخ')
                        ->visible(fn ($get) => $get('dateRange') === 'custom')
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->dateFrom = $state;
                            session()->put('dashboard_date_from', $state);
                            $this->flushCache();
                            $this->dispatch('$refresh');
                        }),

                    DatePicker::make('dateTo')
                        ->label('إلى تاريخ')
                        ->visible(fn ($get) => $get('dateRange') === 'custom')
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->dateTo = $state;
                            session()->put('dashboard_date_to', $state);
                            $this->flushCache();
                            $this->dispatch('$refresh');
                        }),

                    Select::make('branchId')
                        ->label('الفرع')
                        ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->branchId = $state ? (int) $state : null;
                            session()->put('dashboard_finance_branch_id', $this->branchId);
                            $this->flushCache();
                            $this->dispatch('$refresh');
                        }),

                    Select::make('financeTypeId')
                        ->label('نوع المالية')
                        ->options(fn () => FinanceType::where('is_active', true)->get()->pluck('name_text', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->live()
                        ->afterStateUpdated(function ($state) {
                            $this->financeTypeId = $state ? (int) $state : null;
                            session()->put('dashboard_finance_type_id', $this->financeTypeId);
                            $this->flushCache();
                            $this->dispatch('$refresh');
                        }),
                ])
                ->columns(5)
                ->collapsible()
                ->collapsed(true),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'filters';
    }

    protected function flushCache(): void
    {
        $dateRange = $this->dateRange ?? session()->get('dashboard_date_range', 'year');
        $dateFrom = $this->dateFrom ?? session()->get('dashboard_date_from');
        $dateTo = $this->dateTo ?? session()->get('dashboard_date_to');

        if ($dateRange === 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($dateRange === 'year') {
            $from = now()->startOfYear()->startOfDay();
            $to = now()->endOfDay();
        } else {
            $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfYear()->startOfDay();
            $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        }

        $user = Auth::user();
        $branchId = $this->branchId ?? session()->get('dashboard_finance_branch_id') ?? $user?->branch_id ?? null;
        $financeTypeId = $this->financeTypeId ?? session()->get('dashboard_finance_type_id') ?? null;

        $cacheKey = "dashboard_finance_branches_comparison_v4_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";
        Cache::forget($cacheKey);
    }

    protected function getData(): array
    {
        $dateRange = $this->dateRange ?? session()->get('dashboard_date_range', 'year');
        $dateFrom  = $this->dateFrom ?? session()->get('dashboard_date_from');
        $dateTo    = $this->dateTo ?? session()->get('dashboard_date_to');

        if ($dateRange === 'today') {
            $from = now()->startOfDay();
            $to   = now()->endOfDay();
        } elseif ($dateRange === 'year') {
            $from = now()->startOfYear()->startOfDay();
            $to   = now()->endOfDay();
        } else {
            $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfYear()->startOfDay();
            $to   = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        }

        $user = Auth::user();
        $branchId      = $this->branchId ?? session()->get('dashboard_finance_branch_id') ?? $user?->branch_id ?? null;
        $financeTypeId = $this->financeTypeId ?? session()->get('dashboard_finance_type_id') ?? null;

        $cacheKey = "dashboard_finance_branches_comparison_v4_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";

        try {
            return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId, $financeTypeId, $user) {
                $query = BranchTransaction::query()
                    ->whereBetween('finance_branch_transactions.trx_date', [$from, $to])
                    ->where('finance_branch_transactions.status', 'approved')
                    ->join('branches', 'finance_branch_transactions.branch_id', '=', 'branches.id')
                    ->join('finance_types', 'finance_branch_transactions.finance_type_id', '=', 'finance_types.id')
                    ->select(
                        'branches.id as branch_id',
                        'branches.name as branch_name',
                        'finance_types.kind',
                        DB::raw('SUM(finance_branch_transactions.amount) as total_amount')
                    )
                    ->groupBy('branches.id', 'branches.name', 'finance_types.kind');

                if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
                    if (method_exists($user, 'branches')) {
                        $branchIds = $user->branches()->pluck('branches.id')->toArray();
                        if (!empty($branchIds)) {
                            $query->whereIn('finance_branch_transactions.branch_id', $branchIds);
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                    } elseif ($user->branch_id) {
                        $query->where('finance_branch_transactions.branch_id', $user->branch_id);
                    }
                }

                if ($branchId) {
                    $query->where('finance_branch_transactions.branch_id', $branchId);
                }

                if ($financeTypeId) {
                    $query->where('finance_branch_transactions.finance_type_id', $financeTypeId);
                }

                $results = $query->get();

                $branchesQuery = Branch::query()->where('status', 'active');

                if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
                    if (method_exists($user, 'branches')) {
                        $branchIds = $user->branches()->pluck('branches.id')->toArray();
                        if (!empty($branchIds)) {
                            $branchesQuery->whereIn('id', $branchIds);
                        } else {
                            $branchesQuery->whereRaw('1 = 0');
                        }
                    } elseif ($user->branch_id) {
                        $branchesQuery->where('id', $user->branch_id);
                    }
                }

                if ($branchId) {
                    $branchesQuery->where('id', $branchId);
                }

                $branches = $branchesQuery->orderBy('name')->get();

                $labels = [];
                $incomeData = [];
                $expenseData = [];
                $diffData = [];

                foreach ($branches as $branch) {
                    $labels[] = $branch->name;

                    $branchResults = $results->where('branch_id', $branch->id);

                    $income = (float) $branchResults
                        ->where('kind', 'income')
                        ->sum(fn ($item) => (float) ($item->total_amount ?? 0));

                    $expense = (float) $branchResults
                        ->where('kind', 'expense')
                        ->sum(fn ($item) => (float) ($item->total_amount ?? 0));

                    $diff = $income - $expense; // can be negative ✅

                    $incomeData[] = $income;
                    $expenseData[] = $expense;
                    $diffData[] = $diff;
                }

                if (empty($labels)) {
                    $labels[] = 'لا توجد بيانات';
                    $incomeData[] = 0;
                    $expenseData[] = 0;
                    $diffData[] = 0;
                }

                return [
                    'datasets' => [
                        [
                            'label' => 'الإيرادات',
                            'data' => $incomeData,
                            'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                            'borderColor' => 'rgb(34, 197, 94)',
                        ],
                        [
                            'label' => 'المصروفات',
                            'data' => $expenseData,
                            'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                            'borderColor' => 'rgb(239, 68, 68)',
                        ],
                        [
                            'label' => 'الفرق (إيرادات - مصروفات)',
                            'data' => $diffData,
                            'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                            'borderColor' => 'rgb(59, 130, 246)',
                        ],
                    ],
                    'labels' => $labels,
                ];
            });
        } catch (\Exception $e) {
            Log::error('FinanceBranchesComparisonChartWidget Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'datasets' => [
                    [
                        'label' => 'الإيرادات',
                        'data' => [0],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgb(34, 197, 94)',
                    ],
                    [
                        'label' => 'المصروفات',
                        'data' => [0],
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                        'borderColor' => 'rgb(239, 68, 68)',
                    ],
                    [
                        'label' => 'الفرق (إيرادات - مصروفات)',
                        'data' => [0],
                        'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                        'borderColor' => 'rgb(59, 130, 246)',
                    ],
                ],
                'labels' => ['خطأ في تحميل البيانات'],
            ];
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    // ✅ allow negative values to show below zero
                    'beginAtZero' => false,
                    'title' => [
                        'display' => true,
                        'text' => 'المبلغ',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
        ];
    }
}
