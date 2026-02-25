<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingSalaryBatchResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingSalaryBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaryBatches extends ListRecords
{
    protected static string $resource = RentalHousingSalaryBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
