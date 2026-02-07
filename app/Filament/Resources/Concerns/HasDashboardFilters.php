<?php

namespace App\Filament\Resources\Concerns;

use App\Filament\Widgets\Dashboard\DashboardFilterWidget;

trait HasDashboardFilters
{
    protected function getHeaderWidgets(): array
    {
        $widgets = $this->getAdditionalHeaderWidgets();
        
        return [
            DashboardFilterWidget::class,
            ...$widgets,
        ];
    }

    protected function getAdditionalHeaderWidgets(): array
    {
        return [];
    }
}
