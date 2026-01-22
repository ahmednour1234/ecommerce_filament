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
    public ?int $finance_type_id = null;

    protected static ?string $heading = 'أعلى أنواع الإيرادات والمصروفات';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $dateRange = session()->get('dashboard_date_range', 'month');
        $dateFrom  = session()->get('dashboard_date_from');
        $dateTo    = session()->get('dashboard_date_to');

        if ($dateRange === 'today') {
            $from = now()->startOfDay();
            $to   = now()->endOfDay();
        } elseif ($dateRange === 'month') {
            $from = now()->startOfMonth()->startOfDay();
            $to   = now()->endOfDay();
        } else {
            $from = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->startOfMonth()->startOfDay();
            $to   = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();
        }

        $user     = auth()->user();
        $branchId = session()->get('dashboard_finance_branch_id') ?? $user->branch_id ?? $this->branch_id ?? null;
        $financeTypeId = session()->get('dashboard_finance_type_id') ?? $this->finance_type_id ?? null;
        $cacheKey = "dashboard_finance_top_types_{$branchId}_{$financeTypeId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $locale = app()->getLocale();

            $query = BranchTransaction::query()
                ->whereBetween('trx_date', [$from, $to])
                ->join('finance_types', 'finance_branch_transactions.finance_type_id', '=', 'finance_types.id')
                ->select(
                    'finance_types.kind',
                    'finance_types.name as type_name',
                    'finance_branch_transactions.finance_type_id',
                    DB::raw('SUM(finance_branch_transactions.amount) as total_amount')
                )
                ->groupBy('finance_types.kind', 'finance_types.name', 'finance_branch_transactions.finance_type_id');

            $user = auth()->user();

            // صلاحيات الفروع
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

            if ($financeTypeId) {
                $query->where('finance_branch_transactions.finance_type_id', $financeTypeId);
            }

            $results = $query->orderByDesc('total_amount')->get();

            $incomeTypes  = $results->where('kind', 'income')->values()->take(5);
            $expenseTypes = $results->where('kind', 'expense')->values()->take(5);

            // labels = 10 عناصر (5 إيراد + 5 مصروف)
            $incomeLabels = $incomeTypes->map(fn ($x) => $this->normalizeTypeName($x->type_name, $locale))->toArray();
            $expenseLabels = $expenseTypes->map(fn ($x) => $this->normalizeTypeName($x->type_name, $locale))->toArray();

            $labels = array_merge(
                array_map(fn ($n) => "{$n} (إيراد)", $incomeLabels),
                array_map(fn ($n) => "{$n} (مصروف)", $expenseLabels),
            );

            // لازم كل dataset يكون نفس طول labels (10)
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

    /**
     * لأن join بيرجع JSON كـ string ومش بيطبق casts،
     * بنحاول نفكّه لـ array ونجيب ترجمة ar/en صح.
     */
    private function normalizeTypeName($value, string $locale): string
    {
        if (is_array($value)) {
            return (string) ($value[$locale] ?? $value['ar'] ?? $value['en'] ?? reset($value) ?? '');
        }

        if (is_string($value)) {
            $trim = trim($value);

            // لو جاي JSON string
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

    protected function getType(): string
    {
        return 'bar';
    }
}
