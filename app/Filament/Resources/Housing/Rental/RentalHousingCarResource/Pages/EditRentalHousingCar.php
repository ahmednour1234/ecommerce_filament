<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingCarResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingCarResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRentalHousingCar extends BaseEditRecord
{
    protected static string $resource = RentalHousingCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
