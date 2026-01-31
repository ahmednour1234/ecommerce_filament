<?php

namespace App\Filament\Resources\Rental\CancelRefundRequestsResource\Pages;

use App\Filament\Resources\Rental\CancelRefundRequestsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCancelRefundRequests extends ListRecords
{
    protected static string $resource = CancelRefundRequestsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('rental.cancel_refund.create', [], null, 'dashboard') ?: 'Create Request'),
        ];
    }
}
