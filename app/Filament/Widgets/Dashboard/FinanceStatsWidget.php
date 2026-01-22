<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Finance\BranchTransaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class FinanceStatsWidget extends BaseWidget
{
    public ?string $from = null;
    public ?string $to = null;
    public ?int $branch_id = null;
    public ?int $finance_type_id = null;

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
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
        $branchId = session()->get('dashboard_finance_branch_id') ?? $user->branch_id ?? $this->branch_id ?? null;
        $financeTypeId = session()->get('dashboard_finance_type_id') ?? $this->finance_type_id ?? null;

        $cacheKey = "dashboard_finance_stats_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $query = BranchTransaction::query()
                ->whereBetween('trx_date', [$from, $to]);

            $user = auth()->user();
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

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            if ($financeTypeId) {
                $query->where('finance_type_id', $financeTypeId);
            }

            $incomeQuery = (clone $query)->income();
            $expenseQuery = (clone $query)->expense();

            $totalIncome = (float) $incomeQuery->sum('amount');
            $totalExpense = (float) $expenseQuery->sum('amount');
            $netProfit = $totalIncome - $totalExpense;
            $incomeCount = $incomeQuery->count();
            $expenseCount = $expenseQuery->count();

            return [
                Stat::make('إجمالي الإيرادات', Number::currency($totalIncome))
                    ->description('عدد المعاملات: ' . Number::format($incomeCount))
                    ->descriptionIcon('heroicon-o-arrow-trending-up')
                    ->color('success')
                    ->icon('heroicon-o-banknotes'),

                Stat::make('إجمالي المصروفات', Number::currency($totalExpense))
                    ->description('عدد المعاملات: ' . Number::format($expenseCount))
                    ->descriptionIcon('heroicon-o-arrow-trending-down')
                    ->color('danger')
                    ->icon('heroicon-o-currency-dollar'),

                Stat::make('صافي الربح', Number::currency($netProfit))
                    ->description($netProfit >= 0 ? 'ربح إيجابي' : 'خسارة')
                    ->descriptionIcon($netProfit >= 0 ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                    ->color($netProfit >= 0 ? 'success' : 'danger')
                    ->icon('heroicon-o-chart-bar'),

                Stat::make('عدد معاملات الإيرادات', Number::format($incomeCount))
                    ->description('معاملات الإيرادات في الفترة المحددة')
                    ->descriptionIcon('heroicon-o-document-plus')
                    ->color('info')
                    ->icon('heroicon-o-document-text'),

                Stat::make('عدد معاملات المصروفات', Number::format($expenseCount))
                    ->description('معاملات المصروفات في الفترة المحددة')
                    ->descriptionIcon('heroicon-o-document-minus')
                    ->color('warning')
                    ->icon('heroicon-o-document-duplicate'),
            ];
        });
    }
}
