<?php

namespace App\Filament\Resources\Finance\FinanceTypeResource\Pages;

use App\Filament\Resources\Finance\FinanceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinanceTypes extends ListRecords
{
    protected static string $resource = FinanceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.manage_types') ?? false),
        ];
    }
}
