<?php

namespace App\Filament\Widgets\Dashboard;

use App\Models\Sales\Customer;
use App\Models\Sales\Invoice;
use App\Models\Sales\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class SalesOperationsStatsWidget extends BaseWidget
{
    public ?string $from = null;
    public ?string $to = null;
    public ?int $branch_id = null;

    protected static ?int $sort = 3;

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
        $branchId = $user->branch_id ?? $this->branch_id ?? null;

        $cacheKey = "dashboard_sales_stats_{$branchId}_{$from->toDateString()}_{$to->toDateString()}";

        return Cache::remember($cacheKey, 300, function () use ($from, $to, $branchId) {
            $stats = [];

            try {
                $ordersTodayQuery = Order::query()->whereDate('order_date', today());
                $ordersMonthQuery = Order::query()
                    ->whereMonth('order_date', now()->month)
                    ->whereYear('order_date', now()->year);

                if ($branchId) {
                    $ordersTodayQuery->where('branch_id', $branchId);
                    $ordersMonthQuery->where('branch_id', $branchId);
                }

                $ordersToday = $ordersTodayQuery->count();
                $ordersThisMonth = $ordersMonthQuery->count();


            } catch (\Exception $e) {

            }

            try {
                $paidSalesQuery = Invoice::query()
                    ->where(function ($q) {
                        $q->where('status', 'paid')
                            ->orWhereNotNull('paid_at');
                    })
                    ->whereBetween('invoice_date', [$from, $to]);

                if ($branchId) {
                    $paidSalesQuery->where('branch_id', $branchId);
                }

                $totalPaidSales = (float) $paidSalesQuery->sum('total');

            } catch (\Exception $e) {

            }

            try {
                $invoicesQuery = Invoice::query()->whereBetween('invoice_date', [$from, $to]);

                if ($branchId) {
                    $invoicesQuery->where('branch_id', $branchId);
                }

                $totalInvoices = $invoicesQuery->count();


            } catch (\Exception $e) {

            }

            try {
                $avgOrderQuery = Order::query()->whereBetween('order_date', [$from, $to]);

                if ($branchId) {
                    $avgOrderQuery->where('branch_id', $branchId);
                }

                $avgOrderValue = (float) $avgOrderQuery->avg('total');

            } catch (\Exception $e) {

            }

            try {
                $newCustomersQuery = Customer::query()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);

                $newCustomersThisMonth = $newCustomersQuery->count();

            } catch (\Exception $e) {

            }

            return $stats;
        });
    }
}
