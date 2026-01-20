<?php

namespace App\Filament\Resources\Finance\Reports\Widgets;

use App\Models\Finance\BranchTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IncomeExpenseBreakdownDonut extends ChartWidget
{
    protected static ?string $heading = 'Income vs Expense (Breakdown)';

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

        $q = BranchTransaction::query()
            ->whereBetween('trx_date', [$from, $to]);

        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($branchIds)) {
                $q->whereIn('branch_id', $branchIds);
            } else {
                $q->whereRaw('1 = 0');
            }
        }

        $q->when($this->branch_id, fn($qq) => $qq->where('branch_id', $this->branch_id));
        $q->when($this->country_id, fn($qq) => $qq->where('country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('currency_id', $this->currency_id));

        $income  = (clone $q)->whereHas('financeType', fn($qq) => $qq->where('kind', 'income'))->sum('amount');
        $expense = (clone $q)->whereHas('financeType', fn($qq) => $qq->where('kind', 'expense'))->sum('amount');

        return [
            'labels' => ['Income', 'Expense'],
            'datasets' => [
                [
                    'label' => 'Amount',
                    'data' => [(float)$income, (float)$expense],
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
