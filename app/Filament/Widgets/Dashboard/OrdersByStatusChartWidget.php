<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Sales\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrdersByStatusChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    public ?string $from = null;
    public ?string $to = null;
    public ?int $branch_id = null;

    protected static ?string $heading = 'الطلبات حسب الحالة';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

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

        $cacheKey = "dashboard_orders_by_status_{$branchId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $query = Order::query()
                ->whereBetween('order_date', [$from, $to])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status');

            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $results = $query->get();

            $labels = [];
            $data = [];
            $colors = [
                'pending' => 'rgba(59, 130, 246, 0.8)',
                'processing' => 'rgba(251, 191, 36, 0.8)',
                'completed' => 'rgba(34, 197, 94, 0.8)',
                'cancelled' => 'rgba(239, 68, 68, 0.8)',
                'refunded' => 'rgba(168, 85, 247, 0.8)',
            ];

            $statusLabels = [
                'pending' => 'قيد الانتظار',
                'processing' => 'قيد المعالجة',
                'completed' => 'مكتمل',
                'cancelled' => 'ملغي',
                'refunded' => 'مسترد',
            ];

            foreach ($results as $result) {
                $status = $result->status;
                $labels[] = $statusLabels[$status] ?? $status;
                $data[] = (int) $result->count;
            }

            if (empty($labels)) {
                $labels[] = 'لا توجد بيانات';
                $data[] = 0;
            }

            return [
                'datasets' => [
                    [
                        'label' => 'الطلبات',
                        'data' => $data,
                        'backgroundColor' => array_slice(array_values($colors), 0, count($data)),
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
