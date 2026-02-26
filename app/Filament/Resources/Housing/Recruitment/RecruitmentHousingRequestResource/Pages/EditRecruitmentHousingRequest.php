<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRecruitmentHousingRequest extends BaseEditRecord
{
    protected static string $resource = RecruitmentHousingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
