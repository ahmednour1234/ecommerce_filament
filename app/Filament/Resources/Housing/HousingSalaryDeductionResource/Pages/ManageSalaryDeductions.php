<?php

namespace App\Filament\Resources\Housing\HousingSalaryDeductionResource\Pages;

use App\Filament\Resources\Housing\HousingSalaryDeductionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSalaryDeductions extends ManageRecords
{
    protected static string $resource = HousingSalaryDeductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
