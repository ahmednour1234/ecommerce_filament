<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingDriverResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingDriverResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRentalHousingDriver extends BaseEditRecord
{
    protected static string $resource = RentalHousingDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
