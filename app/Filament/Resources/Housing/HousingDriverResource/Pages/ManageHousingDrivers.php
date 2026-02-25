<?php

namespace App\Filament\Resources\Housing\HousingDriverResource\Pages;

use App\Filament\Resources\Housing\HousingDriverResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHousingDrivers extends ManageRecords
{
    protected static string $resource = HousingDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
