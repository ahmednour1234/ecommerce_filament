<?php

namespace App\Filament\Resources\MainCore\ShipmentResource\Pages;

use App\Filament\Resources\MainCore\ShipmentResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use ExportsResourceTable;

    class ListShipments extends ListRecords
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

