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
                
                $stats[] = Stat::make('طلبات اليوم / هذا الشهر', Number::format($ordersToday) . ' / ' . Number::format($ordersThisMonth))
                    ->description('عدد الطلبات')
                    ->descriptionIcon('heroicon-o-shopping-bag')
                    ->color('info')
                    ->icon('heroicon-o-shopping-cart');
            } catch (\Exception $e) {
                $stats[] = Stat::make('طلبات اليوم / هذا الشهر', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
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
                
                $stats[] = Stat::make('إجمالي المبيعات المدفوعة', Number::currency($totalPaidSales))
                    ->description('في الفترة المحددة')
                    ->descriptionIcon('heroicon-o-check-circle')
                    ->color('success')
                    ->icon('heroicon-o-banknotes');
            } catch (\Exception $e) {
                $stats[] = Stat::make('إجمالي المبيعات المدفوعة', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $invoicesQuery = Invoice::query()->whereBetween('invoice_date', [$from, $to]);
                
                if ($branchId) {
                    $invoicesQuery->where('branch_id', $branchId);
                }
                
                $totalInvoices = $invoicesQuery->count();
                
                $stats[] = Stat::make('إجمالي الفواتير', Number::format($totalInvoices))
                    ->description('في الفترة المحددة')
                    ->descriptionIcon('heroicon-o-document-text')
                    ->color('primary')
                    ->icon('heroicon-o-document-duplicate');
            } catch (\Exception $e) {
                $stats[] = Stat::make('إجمالي الفواتير', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $avgOrderQuery = Order::query()->whereBetween('order_date', [$from, $to]);
                
                if ($branchId) {
                    $avgOrderQuery->where('branch_id', $branchId);
                }
                
                $avgOrderValue = (float) $avgOrderQuery->avg('total');
                
                $stats[] = Stat::make('متوسط قيمة الطلب', Number::currency($avgOrderValue))
                    ->description('في الفترة المحددة')
                    ->descriptionIcon('heroicon-o-calculator')
                    ->color('warning')
                    ->icon('heroicon-o-chart-bar');
            } catch (\Exception $e) {
                $stats[] = Stat::make('متوسط قيمة الطلب', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            try {
                $newCustomersQuery = Customer::query()
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
                
                $newCustomersThisMonth = $newCustomersQuery->count();
                
                $stats[] = Stat::make('العملاء الجدد هذا الشهر', Number::format($newCustomersThisMonth))
                    ->description('تم التسجيل هذا الشهر')
                    ->descriptionIcon('heroicon-o-user-plus')
                    ->color('success')
                    ->icon('heroicon-o-user-group');
            } catch (\Exception $e) {
                $stats[] = Stat::make('العملاء الجدد هذا الشهر', 'غير متاح')
                    ->description('غير متاح')
                    ->color('gray')
                    ->icon('heroicon-o-x-circle');
            }

            return $stats;
        });
    }
}
