<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingRequestResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalHousingRequest extends EditRecord
{
    protected static string $resource = RentalHousingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
