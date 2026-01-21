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

        ];
    }
}

