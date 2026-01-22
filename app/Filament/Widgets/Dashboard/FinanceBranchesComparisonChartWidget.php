<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Finance\BranchTransaction;
use App\Models\MainCore\Branch;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FinanceBranchesComparisonChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'مقارنة الفروع المالية';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $dateRange = session()->get('dashboard_date_range', 'month');
        $dateFrom = session()->get('dashboard_date_from');
        $dateTo = session()->get('dashboard_date_to');

        if ($dateRange === 'today') {
            $from = now()->startOfDay();
            $to = now()->endOfDay();
        } elseif ($dateRange === 'month') {
            $from = now()->startOfMonth()->startOfDay();
            $to = now()->endOfDay();
        } else {
            $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfMonth()->startOfDay();
            $to = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        }

        $user = Auth::user();
        $branchId = session()->get('dashboard_finance_branch_id') ?? $user?->branch_id ?? null;
        $financeTypeId = session()->get('dashboard_finance_type_id') ?? null;

        $cacheKey = "dashboard_finance_branches_comparison_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId, $financeTypeId, $user) {
            $query = BranchTransaction::query()
                ->whereBetween('trx_date', [$from, $to])
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

            $branches = Branch::query()
                ->where('status', 'active')
                ->when($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches'), function ($q) use ($user) {
                    if (method_exists($user, 'branches')) {
                        $branchIds = $user->branches()->pluck('branches.id')->toArray();
                        if (!empty($branchIds)) {
                            $q->whereIn('id', $branchIds);
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    } elseif ($user->branch_id) {
                        $q->where('id', $user->branch_id);
                    }
                })
                ->when($branchId, fn ($q) => $q->where('id', $branchId))
                ->orderBy('name')
                ->get();

            $labels = [];
            $incomeData = [];
            $expenseData = [];

            foreach ($branches as $branch) {
                $labels[] = $branch->name;

                $income = $results
                    ->where('branch_id', $branch->id)
                    ->where('kind', 'income')
                    ->sum('total_amount');

                $expense = $results
                    ->where('branch_id', $branch->id)
                    ->where('kind', 'expense')
                    ->sum('total_amount');

                $incomeData[] = (float) $income;
                $expenseData[] = (float) $expense;
            }

            if (empty($labels)) {
                $labels[] = 'لا توجد بيانات';
                $incomeData[] = 0;
                $expenseData[] = 0;
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
                ],
                'labels' => $labels,
            ];
        });
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
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'المبلغ',
                    ],
                ],
            ],
        ];
    }
}
