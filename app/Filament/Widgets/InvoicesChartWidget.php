<?php

namespace App\Filament\Widgets;

use App\Models\Sales\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class InvoicesChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;
    
    protected function getHeading(): string
    {
        return tr('dashboard.stats.invoices_payments');
    }

    protected function getData(): array
    {
        $data = $this->getInvoicesData();

        return [
            'datasets' => [
                [
                    'label' => 'Total Invoices',
                    'data' => $data['total'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Paid Invoices',
                    'data' => $data['paid'],
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Overdue Invoices',
                    'data' => $data['overdue'],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ],
            ],
            'labels' => $data['labels'],
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
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Amount ($)',
                    ],
                ],
            ],
        ];
    }

    protected function getInvoicesData(): array
    {
        $days = 30;
        $labels = [];
        $total = [];
        $paid = [];
        $overdue = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $dayTotal = Invoice::whereDate('invoice_date', $date)->sum('total');
            $dayPaid = Invoice::whereDate('invoice_date', $date)
                ->where(function ($query) {
                    $query->where('status', 'paid')
                        ->orWhereNotNull('paid_at');
                })
                ->sum('total');
            $dayOverdue = Invoice::whereDate('invoice_date', $date)
                ->where('status', 'overdue')
                ->orWhere(function ($query) use ($date) {
                    $query->where('status', '!=', 'paid')
                        ->where('due_date', '<', $date)
                        ->whereNull('paid_at');
                })
                ->sum('total');

            $total[] = round($dayTotal, 2);
            $paid[] = round($dayPaid, 2);
            $overdue[] = round($dayOverdue, 2);
        }

        return [
            'labels' => $labels,
            'total' => $total,
            'paid' => $paid,
            'overdue' => $overdue,
        ];
    }
}

