<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingSalaryBatchResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingSalaryBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaryBatches extends ListRecords
{
    protected static string $resource = RecruitmentHousingSalaryBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
