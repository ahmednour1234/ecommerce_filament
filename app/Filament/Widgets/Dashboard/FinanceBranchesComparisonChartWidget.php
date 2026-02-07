<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Pages\Dashboard;
use App\Services\Dashboard\DashboardService;
use Filament\Widgets\ChartWidget;

class FinanceBranchesComparisonChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = true;

    protected static ?string $heading = 'مقارنة الفروع المالية';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 6;

    protected function getData(): array
    {
        $dashboard = $this->getLivewire();
        
        if (!$dashboard instanceof Dashboard) {
            return $this->getEmptyData();
        }

        $filters = $dashboard->getFilters();
        $service = app(DashboardService::class);
        
        try {
            $data = $service->getBranchComparisonChart($filters);

            if (empty($data['labels']) || ($data['labels'][0] === 'لا توجد بيانات')) {
                return $this->getEmptyData();
            }

            $labels = array_map(function ($label) {
                if (mb_strlen($label) > 15) {
                    return mb_substr($label, 0, 12) . '...';
                }
                return $label;
            }, $data['labels']);

            return [
                'datasets' => [
                    [
                        'label' => 'الإيرادات',
                        'data' => $data['income_data'],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgb(34, 197, 94)',
                    ],
                    [
                        'label' => 'المصروفات',
                        'data' => $data['expense_data'],
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                        'borderColor' => 'rgb(239, 68, 68)',
                    ],
                    [
                        'label' => 'الفرق (إيرادات - مصروفات)',
                        'data' => $data['diff_data'],
                        'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                        'borderColor' => 'rgb(59, 130, 246)',
                    ],
                ],
                'labels' => $labels,
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('FinanceBranchesComparisonChartWidget Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return $this->getEmptyData();
        }
    }

    protected function getEmptyData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات',
                    'data' => [0],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                ],
                [
                    'label' => 'المصروفات',
                    'data' => [0],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
                [
                    'label' => 'الفرق (إيرادات - مصروفات)',
                    'data' => [0],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => ['لا توجد بيانات'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
                'y' => [
                    'beginAtZero' => false,
                    'title' => [
                        'display' => true,
                        'text' => 'المبلغ',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }
}
