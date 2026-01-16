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
            ? "DATE_FORMAT(branch_transactions.transaction_date, '%Y-%m')"
            : "DATE(branch_transactions.transaction_date)";

        // label expression (for output only)
        $labelExpr = $groupExpr . " as period";

        $q = BranchTransaction::query()
            ->from('branch_transactions')
            ->whereBetween('branch_transactions.transaction_date', [$from, $to]);

        // Permission: limit branch
        $user = Auth::user();
        if (! $user?->can('branch_tx.view_all_branches')) {
            $q->where('branch_transactions.branch_id', $user?->branch_id);
        }

        // Filters
        $q->when($this->branch_id, fn ($qq) => $qq->where('branch_transactions.branch_id', $this->branch_id));
        $q->when($this->country_id, fn ($qq) => $qq->where('branch_transactions.country_id', $this->country_id));
        $q->when($this->currency_id, fn ($qq) => $qq->where('branch_transactions.currency_id', $this->currency_id));
        $q->when($this->status, fn ($qq) => $qq->where('branch_transactions.status', $this->status));

        // ✅ Select + GROUP BY RAW + ORDER BY RAW
        $rows = $q->selectRaw("
                {$labelExpr},
                SUM(CASE WHEN branch_transactions.type = 'income' THEN branch_transactions.amount ELSE 0 END) as income,
                SUM(CASE WHEN branch_transactions.type = 'expense' THEN branch_transactions.amount ELSE 0 END) as expense
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
