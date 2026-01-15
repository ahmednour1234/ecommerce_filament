<?php

namespace App\Filament\Resources\Finance\Reports\Widgets;

use App\Models\Finance\BranchTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IncomeExpenseDonutChart extends ChartWidget
{
    protected static ?string $heading = 'Income vs Expense';

    public ?string $from = null;
    public ?string $to = null;
    public ?int $branch_id = null;
    public ?int $country_id = null;
    public ?int $currency_id = null;
    public ?string $status = null;

    protected function getData(): array
    {
        $from = $this->from ? Carbon::parse($this->from)->startOfDay() : now()->startOfMonth();
        $to   = $this->to ? Carbon::parse($this->to)->endOfDay() : now();

        $q = BranchTransaction::query()->whereBetween('transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_id', auth()->user()?->branch_id);
        }

        $q->when($this->branch_id, fn($qq) => $qq->where('branch_id', $this->branch_id));
        $q->when($this->country_id, fn($qq) => $qq->where('country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('currency_id', $this->currency_id));
        $q->when($this->status, fn($qq) => $qq->where('status', $this->status));

        $income  = (clone $q)->where('type','income')->sum('amount');
        $expense = (clone $q)->where('type','expense')->sum('amount');

        return [
            'labels' => [
                tr('reports.columns.income', [], null, 'dashboard'),
                tr('reports.columns.expense', [], null, 'dashboard')
            ],
            'datasets' => [
                ['label' => 'Amount', 'data' => [(float)$income, (float)$expense]],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
