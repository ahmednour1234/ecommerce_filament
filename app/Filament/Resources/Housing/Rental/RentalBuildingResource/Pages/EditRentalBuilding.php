<?php

namespace App\Filament\Resources\Housing\Rental\RentalBuildingResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalBuildingResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRentalBuilding extends BaseEditRecord
{
    protected static string $resource = RentalBuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
