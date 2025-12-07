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
            Stat::make('Total Customers', Number::format($totalCustomers))
                ->description('Active customers')
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->icon('heroicon-o-user-group'),

            Stat::make('Total Orders', Number::format($totalOrders))
                ->description("{$pendingOrders} pending, {$completedOrders} completed")
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('info')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('Total Revenue', Number::currency($totalRevenue))
                ->description('From paid invoices')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Total Invoices', Number::format($totalInvoices))
                ->description('All invoices')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->icon('heroicon-o-document-duplicate'),
        ];
    }
}

