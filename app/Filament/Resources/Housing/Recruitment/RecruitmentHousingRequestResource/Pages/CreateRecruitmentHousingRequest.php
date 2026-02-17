<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecruitmentHousingRequest extends CreateRecord
{
    protected static string $resource = RecruitmentHousingRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['housing_type'] = 'recruitment';
        return $data;
    }
}
