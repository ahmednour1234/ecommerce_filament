<?php

namespace App\Filament\Resources\Housing\HousingLeaveResource\Pages;

use App\Filament\Resources\Housing\HousingLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHousingLeaves extends ManageRecords
{
    protected static string $resource = HousingLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
