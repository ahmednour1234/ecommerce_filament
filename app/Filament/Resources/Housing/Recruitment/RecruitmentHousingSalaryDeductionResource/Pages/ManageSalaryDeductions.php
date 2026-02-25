<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingSalaryDeductionResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingSalaryDeductionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSalaryDeductions extends ManageRecords
{
    protected static string $resource = RecruitmentHousingSalaryDeductionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
