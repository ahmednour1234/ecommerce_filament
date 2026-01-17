<?php

namespace App\Filament\Resources\HR\EmployeeFinancialProfileResource\Pages;

use App\Filament\Resources\HR\EmployeeFinancialProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeFinancialProfiles extends ListRecords
{
    protected static string $resource = EmployeeFinancialProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employee_financial.update') ?? false),
        ];
    }
}
