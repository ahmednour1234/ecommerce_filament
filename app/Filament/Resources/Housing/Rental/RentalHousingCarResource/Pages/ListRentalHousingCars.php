<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingCarResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingCarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalHousingCars extends ListRecords
{
    protected static string $resource = RentalHousingCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
