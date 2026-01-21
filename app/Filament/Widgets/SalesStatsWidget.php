<?php

namespace App\Filament\Widgets;

use App\Models\Sales\Customer;
use App\Models\Sales\Invoice;
use App\Models\Sales\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class SalesStatsWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        $totalCustomers = Customer::where('is_active', true)->count();
        $totalOrders = Order::count();
        $totalInvoices = Invoice::count();

        $totalRevenue = Invoice::where('status', 'paid')
            ->orWhereNotNull('paid_at')
            ->sum('total');

        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();

        return [

        ];
    }
}

