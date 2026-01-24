<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinanceBranchesComparisonChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = true;

    protected static ?string $heading = 'مقارنة الفروع المالية';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $dateRange = session()->get('dashboard_date_range', 'month');
        $dateFrom  = session()->get('dashboard_date_from');
        $dateTo    = session()->get('dashboard_date_to');



        $user = Auth::user();
        $branchId      = session()->get('dashboard_finance_branch_id') ?? $user?->branch_id ?? null;
        $financeTypeId = session()->get('dashboard_finance_type_id') ?? null;

        // ✅ change cache key version to avoid old cached data
        $cacheKey = "dashboard_finance_branches_comparison_v3_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";

        try {
            return Cache::remember($cacheKey, 300, function () use ( $branchId, $financeTypeId, $user) {
                $query = BranchTransaction::query()
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
