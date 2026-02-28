<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingCarResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingCarResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRecruitmentHousingCar extends BaseEditRecord
{
    protected static string $resource = RecruitmentHousingCarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
