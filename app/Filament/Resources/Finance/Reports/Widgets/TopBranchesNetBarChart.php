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

        $q = BranchTransaction::query()->with('branch')
            ->whereBetween('transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_id', auth()->user()?->branch_id);
        }

        $q->when($this->country_id, fn($qq) => $qq->where('country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('currency_id', $this->currency_id));
        $q->when($this->status, fn($qq) => $qq->where('status', $this->status));

        $rows = $q->selectRaw("
                branch_id,
                SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
            ")
            ->groupBy('branch_id')
            ->orderByRaw('(income - expense) DESC')
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
