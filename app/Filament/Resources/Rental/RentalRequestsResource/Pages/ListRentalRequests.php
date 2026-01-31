<?php

namespace App\Filament\Resources\Rental\RentalRequestsResource\Pages;

use App\Filament\Resources\Rental\RentalRequestsResource;
use App\Filament\Widgets\Rental\RequestStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalRequests extends ListRecords
{
    protected static string $resource = RentalRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('rental.requests.create', [], null, 'dashboard') ?: 'Create Request'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RequestStatsWidget::class,
        ];
    }
}
