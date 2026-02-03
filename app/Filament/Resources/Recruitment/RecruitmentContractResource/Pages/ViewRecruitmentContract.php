<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRecruitmentContract extends ViewRecord
{
    protected static string $resource = RecruitmentContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
