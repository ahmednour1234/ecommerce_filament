<?php

namespace App\Filament\Resources\Finance\Reports\Widgets;

use App\Models\Finance\BranchTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TopBranchesNetBarChart extends ChartWidget
{
    protected static ?string $heading = 'Top Branches (Net = Income - Expense)';

    public ?string $from = null;
    public ?string $to = null;
    public ?int $country_id = null;
    public ?int $currency_id = null;
    public ?string $status = null;

    protected function getData(): array
    {
        $from = $this->from ? Carbon::parse($this->from)->startOfDay() : now()->startOfMonth();
        $to   = $this->to ? Carbon::parse($this->to)->endOfDay() : now();

        $q = BranchTransaction::query()
            ->with('branch')
            ->join('finance_types', 'finance_branch_transactions.finance_type_id', '=', 'finance_types.id')
            ->whereBetween('finance_branch_transactions.trx_date', [$from, $to]);

        $user = auth()->user();
        if ($user && !$user->hasRole('super_admin') && !$user->can('finance.view_all_branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            if (!empty($branchIds)) {
                $q->whereIn('finance_branch_transactions.branch_id', $branchIds);
            } else {
                $q->whereRaw('1 = 0');
            }
        }

        $q->when($this->country_id, fn($qq) => $qq->where('finance_branch_transactions.country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('finance_branch_transactions.currency_id', $this->currency_id));

        $rows = $q->selectRaw("
                finance_branch_transactions.branch_id,
                SUM(CASE WHEN finance_types.kind='income' THEN finance_branch_transactions.amount ELSE 0 END) as income,
                SUM(CASE WHEN finance_types.kind='expense' THEN finance_branch_transactions.amount ELSE 0 END) as expense
            ")
            ->groupBy('finance_branch_transactions.branch_id')
            ->orderByRaw('(SUM(CASE WHEN finance_types.kind=\'income\' THEN finance_branch_transactions.amount ELSE 0 END) - SUM(CASE WHEN finance_types.kind=\'expense\' THEN finance_branch_transactions.amount ELSE 0 END)) DESC')
            ->limit(10)
            ->get();

        $labels = $rows->map(fn($r) => $r->branch?->name ?? ('Branch #' . $r->branch_id))->toArray();
        $net    = $rows->map(fn($r) => (float)$r->income - (float)$r->expense)->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Net', 'data' => $net],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
