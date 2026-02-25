<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingDriverResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingDriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRentalHousingDrivers extends ManageRecords
{
    protected static string $resource = RentalHousingDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
