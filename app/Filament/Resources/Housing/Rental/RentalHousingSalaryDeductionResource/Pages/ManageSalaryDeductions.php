<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingSalaryDeductionResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingSalaryDeductionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSalaryDeductions extends ManageRecords
{
    protected static string $resource = RentalHousingSalaryDeductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
