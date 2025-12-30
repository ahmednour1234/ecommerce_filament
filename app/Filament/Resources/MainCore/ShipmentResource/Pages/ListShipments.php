<?php

namespace App\Filament\Resources\MainCore\ShipmentResource\Pages;

use App\Filament\Resources\MainCore\ShipmentResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShipments extends ListRecords
{
    use ExportsResourceTable;
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

