<?php

namespace App\Filament\Widgets;

use App\Models\Sales\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Orders Over Time';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = $this->getOrdersData();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data['orders'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Revenue',
                    'data' => $data['revenue'],
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Number of Orders',
                    ],
                ],
                'y1' => [
                    'position' => 'right',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Revenue ($)',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getOrdersData(): array
    {
        $days = 30;
        $labels = [];
        $orders = [];
        $revenue = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $dayOrders = Order::whereDate('order_date', $date)->count();
            $dayRevenue = Order::whereDate('order_date', $date)
                ->where('status', 'completed')
                ->sum('total');

            $orders[] = $dayOrders;
            $revenue[] = round($dayRevenue, 2);
        }

        return [
            'labels' => $labels,
            'orders' => $orders,
            'revenue' => $revenue,
        ];
    }
}

