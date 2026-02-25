<?php

namespace App\Filament\Resources\Housing\HousingSalaryBatchResource\Pages;

use App\Filament\Resources\Housing\HousingSalaryBatchResource;
use Filament\Resources\Pages\Page;

class PrintSalaryBatch extends Page
{
    protected static string $resource = HousingSalaryBatchResource::class;
    protected static string $view = 'filament.resources.housing.housing-salary-batch-resource.pages.print-salary-batch';

    public $record;

    public function mount($record): void
    {
        $this->record = \App\Models\Housing\HousingSalaryBatch::findOrFail($record);
    }
}
