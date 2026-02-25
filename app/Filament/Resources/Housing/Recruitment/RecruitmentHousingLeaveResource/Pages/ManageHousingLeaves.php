<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingLeaveResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHousingLeaves extends ManageRecords
{
    protected static string $resource = RecruitmentHousingLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
