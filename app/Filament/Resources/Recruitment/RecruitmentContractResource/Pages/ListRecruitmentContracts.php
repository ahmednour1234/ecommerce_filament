<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecruitmentContracts extends ListRecords
{
    protected static string $resource = RecruitmentContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('recruitment_contract.actions.create', [], null, 'dashboard') ?: 'Create Contract'),
        ];
    }
}
