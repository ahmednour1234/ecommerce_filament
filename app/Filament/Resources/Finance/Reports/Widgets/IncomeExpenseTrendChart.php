<?php

namespace App\Filament\Resources\Finance\Reports\Widgets;

use App\Models\Finance\BranchTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class IncomeExpenseTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Income vs Expense Trend';

    public ?string $from = null;
    public ?string $to = null;

    public ?int $branch_id = null;
    public ?int $country_id = null;
    public ?int $currency_id = null;

    public ?string $status = null;     // e.g. approved/pending...
    public ?string $group_by = 'day';  // day|month

    protected function getData(): array
    {
        $from = $this->from ? Carbon::parse($this->from)->startOfDay() : now()->startOfMonth();
        $to   = $this->to   ? Carbon::parse($this->to)->endOfDay()   : now()->endOfDay();

        // ✅ IMPORTANT: use raw expression for GROUP BY/ORDER BY (not alias)
        $groupExpr = ($this->group_by === 'month')
            ? "DATE_FORMAT(finance_branch_transactions.trx_date, '%Y-%m')"
            : "DATE(finance_branch_transactions.trx_date)";

        // label expression (for output only)
        $labelExpr = $groupExpr . " as period";

        $q = BranchTransaction::query()
            ->from('finance_branch_transactions')
            ->join('finance_types', 'finance_branch_transactions.finance_type_id', '=', 'finance_types.id')
            ->whereBetween('finance_branch_transactions.trx_date', [$from, $to]);

        // Permission: limit branch
        $user = Auth::user();
        if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($branchIds)) {
                $q->whereIn('finance_branch_transactions.branch_id', $branchIds);
            } else {
                $q->whereRaw('1 = 0');
            }
        }

        // Filters
        $q->when($this->branch_id, fn ($qq) => $qq->where('finance_branch_transactions.branch_id', $this->branch_id));
        $q->when($this->country_id, fn ($qq) => $qq->where('finance_branch_transactions.country_id', $this->country_id));
        $q->when($this->currency_id, fn ($qq) => $qq->where('finance_branch_transactions.currency_id', $this->currency_id));

        // ✅ Select + GROUP BY RAW + ORDER BY RAW
        $rows = $q->selectRaw("
                {$labelExpr},
                SUM(CASE WHEN finance_types.kind = 'income' THEN finance_branch_transactions.amount ELSE 0 END) as income,
                SUM(CASE WHEN finance_types.kind = 'expense' THEN finance_branch_transactions.amount ELSE 0 END) as expense
            ")
            ->groupByRaw($groupExpr)
            ->orderByRaw($groupExpr . " asc")
            ->get();

        return [
            'labels' => $rows->pluck('period')->toArray(),
            'datasets' => [
                [
                    'label' => tr('reports.columns.income', [], null, 'dashboard'),
                    'data'  => $rows->pluck('income')->map(fn ($v) => (float) $v)->toArray(),
                ],
                [
                    'label' => tr('reports.columns.expense', [], null, 'dashboard'),
                    'data'  => $rows->pluck('expense')->map(fn ($v) => (float) $v)->toArray(),
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
