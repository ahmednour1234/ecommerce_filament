<?php

namespace App\Filament\Resources\Housing\Rental\RentalHousingSalaryBatchResource\Pages;

use App\Filament\Resources\Housing\Rental\RentalHousingSalaryBatchResource;
use Filament\Resources\Pages\Page;

class PrintSalaryBatch extends Page
{
    protected static string $resource = RentalHousingSalaryBatchResource::class;
    protected static string $view = 'filament.resources.housing.rental.rental-housing-salary-batch-resource.pages.print-salary-batch';

    public $record;

    public function mount($record): void
    {
        $this->record = \App\Models\Housing\HousingSalaryBatch::findOrFail($record);
    }
}
