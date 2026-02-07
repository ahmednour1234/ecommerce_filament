<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Pages\Dashboard;
use App\Services\Dashboard\DashboardService;
use Filament\Widgets\ChartWidget;

class FinanceTopTypesWidget extends ChartWidget
{
    protected static ?string $heading = 'أعلى أنواع الإيرادات والمصروفات';
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $dashboard = $this->getOwner();
        
        if (!$dashboard instanceof Dashboard) {
            return $this->getEmptyData();
        }

        $filters = $dashboard->getFilters();
        $service = app(DashboardService::class);
        $data = $service->getTopIncomeExpenseTypes($filters);

        if (empty($data['labels']) || (array_sum($data['income_data']) == 0 && array_sum($data['expense_data']) == 0)) {
            return $this->getEmptyData();
        }

        $labels = array_map(function ($label) {
            if (mb_strlen($label) > 20) {
                return mb_substr($label, 0, 17) . '...';
            }
            return $label;
        }, $data['labels']);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'الإيرادات',
                    'data' => $data['income_data'],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                ],
                [
                    'label' => 'المصروفات',
                    'data' => $data['expense_data'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                ],
            ],
        ];
    }

    protected function getEmptyData(): array
    {
        return [
            'labels' => ['لا توجد بيانات'],
            'datasets' => [
                [
                    'label' => 'الإيرادات',
                    'data' => [0],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                ],
                [
                    'label' => 'المصروفات',
                    'data' => [0],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                ],
            ],
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
