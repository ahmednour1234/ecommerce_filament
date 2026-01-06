<?php

namespace App\Filament\Widgets;

use App\Models\Sales\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $todayOrders = Order::whereDate('order_date', today())->count();
        $thisMonthOrders = Order::whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->count();
        
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        $todayRevenue = Order::whereDate('order_date', today())
            ->where('status', 'completed')
            ->sum('total');
        
        $thisMonthRevenue = Order::whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        $avgOrderValue = Order::where('status', 'completed')
            ->avg('total') ?? 0;

        return [
            Stat::make(tr('dashboard.stats.todays_orders'), Number::format($todayOrders))
                ->description(tr('dashboard.stats.todays_orders_description'))
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make(tr('dashboard.stats.this_month_orders'), Number::format($thisMonthOrders))
                ->description(tr('dashboard.stats.this_month_orders_description'))
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary')
                ->icon('heroicon-o-chart-bar'),

            Stat::make(tr('dashboard.stats.pending_orders'), Number::format($pendingOrders))
                ->description("{$completedOrders} " . tr('dashboard.stats.status.completed') . ", {$cancelledOrders} " . tr('dashboard.stats.status.cancelled'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make(tr('dashboard.stats.todays_revenue'), Number::currency($todayRevenue))
                ->description(tr('dashboard.stats.todays_revenue_description'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make(tr('dashboard.stats.monthly_revenue'), Number::currency($thisMonthRevenue))
                ->description(tr('dashboard.stats.monthly_revenue_description'))
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success')
                ->icon('heroicon-o-chart-pie'),

            Stat::make(tr('dashboard.stats.average_order_value'), Number::currency($avgOrderValue))
                ->description(tr('dashboard.stats.average_order_value_description'))
                ->descriptionIcon('heroicon-o-calculator')
                ->color('info')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}

