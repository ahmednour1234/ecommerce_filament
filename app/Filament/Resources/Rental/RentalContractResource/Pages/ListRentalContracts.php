<?php

namespace App\Filament\Resources\Rental\RentalContractResource\Pages;

use App\Filament\Resources\Rental\RentalContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalContracts extends ListRecords
{
    protected static string $resource = RentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('rental.contracts.create', [], null, 'dashboard') ?: 'Create Contract'),
        ];
    }
}
