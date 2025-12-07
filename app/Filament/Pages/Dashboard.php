<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AccountingSummaryWidget;
use App\Filament\Widgets\CatalogStatsWidget;
use App\Filament\Widgets\OrderStatsWidget;
use App\Filament\Widgets\SalesStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            SalesStatsWidget::class,
            CatalogStatsWidget::class,
            OrderStatsWidget::class,
            AccountingSummaryWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\OrdersChartWidget::class,
            \App\Filament\Widgets\InvoicesChartWidget::class,
            \App\Filament\Widgets\SalesByStatusWidget::class,
        ];
    }
}

