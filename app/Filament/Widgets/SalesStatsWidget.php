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
            Stat::make(tr('dashboard.stats.total_customers'), Number::format($totalCustomers))
                ->description(tr('dashboard.stats.total_customers_description'))
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->icon('heroicon-o-user-group'),

            Stat::make(tr('dashboard.stats.total_orders'), Number::format($totalOrders))
                ->description("{$pendingOrders} " . tr('dashboard.stats.status.pending') . ", {$completedOrders} " . tr('dashboard.stats.status.completed'))
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('info')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make(tr('dashboard.stats.total_revenue'), Number::currency($totalRevenue))
                ->description(tr('dashboard.stats.total_revenue_description'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make(tr('dashboard.stats.total_invoices'), Number::format($totalInvoices))
                ->description(tr('dashboard.stats.total_invoices_description'))
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->icon('heroicon-o-document-duplicate'),
        ];
    }
}

