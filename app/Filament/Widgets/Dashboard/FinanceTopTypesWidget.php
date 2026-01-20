<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Finance\BranchTransaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FinanceTopTypesWidget extends ChartWidget
{
    public ?string $from = null;
    public ?string $to = null;
    public ?int $branch_id = null;

    protected static ?string $heading = 'أعلى أنواع الإيرادات والمصروفات';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

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
        
        $user = auth()->user();
        $branchId = $user->branch_id ?? $this->branch_id ?? null;

        $cacheKey = "dashboard_finance_top_types_{$branchId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $query = BranchTransaction::query()
                ->whereBetween('trx_date', [$from, $to])
                ->join('finance_types', 'finance_branch_transactions.finance_type_id', '=', 'finance_types.id')
                ->select(
                    'finance_types.kind',
                    'finance_types.name',
                    'finance_branch_transactions.finance_type_id',
                    DB::raw('SUM(finance_branch_transactions.amount) as total_amount'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('finance_types.kind', 'finance_types.name', 'finance_branch_transactions.finance_type_id');

            $user = auth()->user();
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

            $results = $query->orderByDesc('total_amount')->get();

            $incomeTypes = $results->where('kind', 'income')->take(5);
            $expenseTypes = $results->where('kind', 'expense')->take(5);

            $labels = [];
            $incomeData = [];
            $expenseData = [];

            foreach ($incomeTypes as $item) {
                $name = is_array($item->name) ? ($item->name['ar'] ?? $item->name['en'] ?? '') : $item->name;
                $labels[] = $name . ' (إيراد)';
                $incomeData[] = (float) $item->total_amount;
            }

            foreach ($expenseTypes as $item) {
                $name = is_array($item->name) ? ($item->name['ar'] ?? $item->name['en'] ?? '') : $item->name;
                $labels[] = $name . ' (مصروف)';
                $expenseData[] = (float) $item->total_amount;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'الإيرادات',
                        'data' => $incomeData,
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    ],
                    [
                        'label' => 'المصروفات',
                        'data' => $expenseData,
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    ],
                ],
            ];
        });
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
