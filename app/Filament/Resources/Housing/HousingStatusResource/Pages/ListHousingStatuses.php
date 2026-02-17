<?php

namespace App\Filament\Resources\Housing\HousingStatusResource\Pages;

use App\Filament\Resources\Housing\HousingStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHousingStatuses extends ListRecords
{
    protected static string $resource = HousingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
