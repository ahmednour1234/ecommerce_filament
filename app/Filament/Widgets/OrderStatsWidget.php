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
            Stat::make('Today\'s Orders', Number::format($todayOrders))
                ->description('Orders placed today')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('This Month Orders', Number::format($thisMonthOrders))
                ->description('Orders this month')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary')
                ->icon('heroicon-o-chart-bar'),

            Stat::make('Pending Orders', Number::format($pendingOrders))
                ->description("{$completedOrders} completed, {$cancelledOrders} cancelled")
                ->descriptionIcon('heroicon-o-clock')
                ->color($pendingOrders > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-exclamation-triangle'),

            Stat::make('Today\'s Revenue', Number::currency($todayRevenue))
                ->description('From completed orders')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Monthly Revenue', Number::currency($thisMonthRevenue))
                ->description('This month\'s total')
                ->descriptionIcon('heroicon-o-chart-bar')
                ->color('success')
                ->icon('heroicon-o-chart-pie'),

            Stat::make('Average Order Value', Number::currency($avgOrderValue))
                ->description('From completed orders')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('info')
                ->icon('heroicon-o-chart-bar'),
        ];
    }
}

