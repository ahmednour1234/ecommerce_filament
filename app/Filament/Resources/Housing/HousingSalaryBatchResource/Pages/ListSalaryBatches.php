<?php

namespace App\Filament\Resources\Housing\HousingSalaryBatchResource\Pages;

use App\Filament\Resources\Housing\HousingSalaryBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaryBatches extends ListRecords
{
    protected static string $resource = HousingSalaryBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
