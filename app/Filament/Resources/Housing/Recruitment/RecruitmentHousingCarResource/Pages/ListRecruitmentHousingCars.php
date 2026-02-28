<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingCarResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingCarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecruitmentHousingCars extends ListRecords
{
    protected static string $resource = RecruitmentHousingCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
