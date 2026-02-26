<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingRequestResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingRequestResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRentalHousingRequest extends BaseEditRecord
{
    protected static string $resource = RentalHousingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
