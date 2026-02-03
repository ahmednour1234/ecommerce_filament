<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecruitmentContract extends EditRecord
{
    protected static string $resource = RecruitmentContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
