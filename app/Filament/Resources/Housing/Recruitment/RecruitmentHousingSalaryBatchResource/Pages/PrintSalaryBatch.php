<?php

namespace App\Filament\Resources\Housing\Recruitment\RecruitmentHousingSalaryBatchResource\Pages;

use App\Filament\Resources\Housing\Recruitment\RecruitmentHousingSalaryBatchResource;
use Filament\Resources\Pages\Page;

class PrintSalaryBatch extends Page
{
    protected static string $resource = RecruitmentHousingSalaryBatchResource::class;
    protected static string $view = 'filament.resources.housing.recruitment.recruitment-housing-salary-batch-resource.pages.print-salary-batch';

    public $record;

    public function mount($record): void
    {
        $this->record = \App\Models\Housing\HousingSalaryBatch::findOrFail($record);
    }
}
