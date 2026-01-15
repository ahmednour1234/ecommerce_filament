<?php

namespace App\Filament\Resources\Finance\Reports\Widgets;

use App\Models\Finance\BranchTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IncomeExpenseTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Income vs Expense Trend';

    public ?string $from = null;
    public ?string $to = null;
    public ?int $branch_id = null;
    public ?int $country_id = null;
    public ?int $currency_id = null;
    public ?string $status = null;
    public ?string $group_by = 'day';

    protected function getData(): array
    {
        $from = $this->from ? Carbon::parse($this->from)->startOfDay() : now()->startOfMonth();
        $to   = $this->to ? Carbon::parse($this->to)->endOfDay() : now();

        $dateExpr = $this->group_by === 'month'
            ? "DATE_FORMAT(transaction_date, '%Y-%m')"
            : "DATE(transaction_date)";

        $q = BranchTransaction::query()
            ->whereBetween('transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_id', auth()->user()?->branch_id);
        }

        $q->when($this->branch_id, fn($qq) => $qq->where('branch_id', $this->branch_id));
        $q->when($this->country_id, fn($qq) => $qq->where('country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('currency_id', $this->currency_id));
        $q->when($this->status, fn($qq) => $qq->where('status', $this->status));

        $rows = $q->selectRaw("
                {$dateExpr} as d,
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
            ")
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        return [
            'labels' => $rows->pluck('d')->toArray(),
            'datasets' => [
                ['label' => tr('reports.columns.income', [], null, 'dashboard'), 'data' => $rows->pluck('income')->map(fn($v)=>(float)$v)->toArray()],
                ['label' => tr('reports.columns.expense', [], null, 'dashboard'), 'data' => $rows->pluck('expense')->map(fn($v)=>(float)$v)->toArray()],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
