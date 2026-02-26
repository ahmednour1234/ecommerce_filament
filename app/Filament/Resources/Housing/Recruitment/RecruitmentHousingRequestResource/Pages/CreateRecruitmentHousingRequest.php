<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateRecruitmentHousingRequest extends BaseCreateRecord
{
    protected static string $resource = RecruitmentHousingRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['housing_type'] = 'recruitment';
        return $data;
    }
}
