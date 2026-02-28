<?php

namespace App\Filament\Resources\Housing\Rental\RentalBuildingResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalBuildingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalBuildings extends ListRecords
{
    protected static string $resource = RentalBuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
