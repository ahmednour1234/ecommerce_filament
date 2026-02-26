<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingRequestResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingRequestResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateRentalHousingRequest extends BaseCreateRecord
{
    protected static string $resource = RentalHousingRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['housing_type'] = 'rental';
        return $data;
    }
}
