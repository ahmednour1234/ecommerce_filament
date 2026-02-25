<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingLeaveResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHousingLeaves extends ManageRecords
{
    protected static string $resource = RentalHousingLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
