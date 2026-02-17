<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecruitmentHousingRequest extends EditRecord
{
    protected static string $resource = RecruitmentHousingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
