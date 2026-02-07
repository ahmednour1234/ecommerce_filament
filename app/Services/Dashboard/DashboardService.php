<?php

namespace App\Services\Dashboard;

use App\Models\Finance\BranchTransaction;
use App\Models\Finance\FinanceType;
use App\Models\MainCore\Branch;
use App\Models\Sales\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getOrderStats(array $filters): array
    {
        $query = Order::query();
        $this->applyOrderFilters($query, $filters);

        $stats = [
            'pending' => 0,
            'review' => 0,
            'waiting' => 0,
            'approved' => 0,
            'rejected' => 0,
            'converted' => 0,
            'processing' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'refunded' => 0,
            'total' => 0,
        ];

        $results = $query->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($results as $result) {
            $status = $result->status;
            $count = (int) $result->count;
            
            if (isset($stats[$status])) {
                $stats[$status] = $count;
            }
            $stats['total'] += $count;
        }

        return $stats;
    }

    public function getFinancialKpis(array $filters): array
    {
        $query = BranchTransaction::query()
            ->where('status', 'approved');

        $this->applyFinancialFilters($query, $filters);

        $incomeQuery = (clone $query)->income();
        $expenseQuery = (clone $query)->expense();

        $totalIncome = (float) $incomeQuery->sum('amount');
        $totalExpense = (float) $expenseQuery->sum('amount');
        $netProfit = $totalIncome - $totalExpense;

        $incomeCount = $incomeQuery->count();
        $expenseCount = $expenseQuery->count();

        return [
            'total_revenue' => $totalIncome,
            'total_expenses' => $totalExpense,
            'net_profit' => $netProfit,
            'revenue_count' => $incomeCount,
            'expense_count' => $expenseCount,
        ];
    }

    public function getBranchFinancialSummary(array $filters): array
    {
        $query = BranchTransaction::query()
            ->where('status', 'approved');

        $this->applyFinancialFilters($query, $filters);

        $user = Auth::user();
        $branchesQuery = Branch::where('status', 'active');

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

        if (isset($filters['branch_id']) && $filters['branch_id']) {
            $branchesQuery->where('id', $filters['branch_id']);
        }

        $branches = $branchesQuery->get();
        $rows = [];
        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($branches as $branch) {
            $branchQuery = (clone $query)->where('branch_id', $branch->id);

            if (isset($filters['finance_type_id']) && $filters['finance_type_id']) {
                $branchQuery->where('finance_type_id', $filters['finance_type_id']);
            }

            $incomeQuery = (clone $branchQuery)->income();
            $expenseQuery = (clone $branchQuery)->expense();

            $income = (float) $incomeQuery->sum('amount');
            $expense = (float) $expenseQuery->sum('amount');
            $net = $income - $expense;

            $totalIncome += $income;
            $totalExpense += $expense;

            $rows[] = [
                'id' => 'branch_' . str_pad($branch->id, 10, '0', STR_PAD_LEFT),
                'branch_name' => $branch->name,
                'income' => $income,
                'expense' => $expense,
                'net' => $net,
            ];
        }

        if (!empty($rows)) {
            $rows[] = [
                'id' => 'zzz_total',
                'branch_name' => 'الإجمالي',
                'income' => $totalIncome,
                'expense' => $totalExpense,
                'net' => $totalIncome - $totalExpense,
            ];
        }

        return $rows;
    }

    public function getTopIncomeExpenseTypes(array $filters): array
    {
        $query = BranchTransaction::query()
            ->where('status', 'approved')
            ->join('finance_types', 'finance_branch_transactions.finance_type_id', '=', 'finance_types.id')
            ->select(
                'finance_types.kind',
                'finance_types.name as type_name',
                'finance_branch_transactions.finance_type_id',
                DB::raw('SUM(finance_branch_transactions.amount) as total_amount')
            )
            ->groupBy('finance_types.kind', 'finance_types.name', 'finance_branch_transactions.finance_type_id');

        $this->applyFinancialFilters($query, $filters);

        $results = $query->orderByDesc('total_amount')->get();

        $incomeTypes = $results->where('kind', 'income')->values()->take(5);
        $expenseTypes = $results->where('kind', 'expense')->values()->take(5);

        $locale = app()->getLocale();

        $incomeLabels = $incomeTypes->map(fn ($x) => $this->normalizeTypeName($x->type_name, $locale))->toArray();
        $expenseLabels = $expenseTypes->map(fn ($x) => $this->normalizeTypeName($x->type_name, $locale))->toArray();

        $labels = array_merge(
            array_map(fn ($n) => "{$n} (إيراد)", $incomeLabels),
            array_map(fn ($n) => "{$n} (مصروف)", $expenseLabels),
        );

        $incomeData = array_merge(
            $incomeTypes->map(fn ($x) => (float) $x->total_amount)->toArray(),
            array_fill(0, count($expenseTypes), 0)
        );

        $expenseData = array_merge(
            array_fill(0, count($incomeTypes), 0),
            $expenseTypes->map(fn ($x) => (float) $x->total_amount)->toArray()
        );

        return [
            'labels' => $labels,
            'income_data' => $incomeData,
            'expense_data' => $expenseData,
        ];
    }

    public function getBranchComparisonChart(array $filters): array
    {
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

        $this->applyFinancialFilters($query, $filters);

        $results = $query->get();

        $user = Auth::user();
        $branchesQuery = Branch::where('status', 'active');

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

        if (isset($filters['branch_id']) && $filters['branch_id']) {
            $branchesQuery->where('id', $filters['branch_id']);
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

            $diff = $income - $expense;

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
            'labels' => $labels,
            'income_data' => $incomeData,
            'expense_data' => $expenseData,
            'diff_data' => $diffData,
        ];
    }

    protected function applyOrderFilters(Builder $query, array $filters): void
    {
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('order_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('order_date', '<=', $filters['date_to']);
        }

        if (isset($filters['branch_id']) && $filters['branch_id']) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['order_status']) && $filters['order_status'] && $filters['order_status'] !== 'all') {
            $query->where('status', $filters['order_status']);
        }

        $user = Auth::user();
        if ($user && !$user->hasRole('super_admin')) {
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        }
    }

    protected function applyFinancialFilters(Builder $query, array $filters): void
    {
        if (isset($filters['date_from']) && $filters['date_from']) {
            $query->where('trx_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $query->where('trx_date', '<=', $filters['date_to']);
        }

        if (isset($filters['branch_id']) && $filters['branch_id']) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['transaction_type']) && $filters['transaction_type'] !== 'all') {
            if ($filters['transaction_type'] === 'revenue') {
                $query->income();
            } elseif ($filters['transaction_type'] === 'expense') {
                $query->expense();
            }
        }

        if (isset($filters['revenue_type_id']) && $filters['revenue_type_id']) {
            $query->where('finance_type_id', $filters['revenue_type_id'])
                ->whereHas('financeType', fn($q) => $q->where('kind', 'income'));
        }

        if (isset($filters['expense_type_id']) && $filters['expense_type_id']) {
            $query->where('finance_type_id', $filters['expense_type_id'])
                ->whereHas('financeType', fn($q) => $q->where('kind', 'expense'));
        }

        if (isset($filters['finance_type_id']) && $filters['finance_type_id']) {
            $query->where('finance_type_id', $filters['finance_type_id']);
        }

        if (isset($filters['currency_id']) && $filters['currency_id']) {
            $query->where('currency_id', $filters['currency_id']);
        }

        $user = Auth::user();
        if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
            if (method_exists($user, 'branches')) {
                $branchIds = $user->branches()->pluck('branches.id')->toArray();
                if (!empty($branchIds)) {
                    $query->whereIn('branch_id', $branchIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            } elseif ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            }
        }
    }

    private function normalizeTypeName($value, string $locale): string
    {
        if (is_array($value)) {
            return (string) ($value[$locale] ?? $value['ar'] ?? $value['en'] ?? reset($value) ?? '');
        }

        if (is_string($value)) {
            $trim = trim($value);

            if ($trim !== '' && (str_starts_with($trim, '{') || str_starts_with($trim, '['))) {
                $decoded = json_decode($trim, true);

                if (is_array($decoded)) {
                    return (string) ($decoded[$locale] ?? $decoded['ar'] ?? $decoded['en'] ?? reset($decoded) ?? '');
                }
            }

            return $value;
        }

        return '';
    }
}
