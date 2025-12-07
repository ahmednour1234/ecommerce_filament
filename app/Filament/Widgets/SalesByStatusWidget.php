<?php

namespace App\Filament\Widgets;

use App\Models\Sales\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SalesByStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Orders by Status';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $statuses = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = array_keys($statuses);
        $data = array_values($statuses);

        $colors = [
            'pending' => 'rgb(251, 191, 36)',
            'processing' => 'rgb(59, 130, 246)',
            'completed' => 'rgb(16, 185, 129)',
            'cancelled' => 'rgb(239, 68, 68)',
            'refunded' => 'rgb(107, 114, 128)',
        ];

        $backgroundColors = array_map(function ($status) use ($colors) {
            return $colors[$status] ?? 'rgb(156, 163, 175)';
        }, $labels);

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                ],
            ],
            'labels' => array_map('ucfirst', $labels),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}

