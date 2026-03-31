<?php

namespace App\Filament\Resources\Rental\RentalContractResource\Pages;

use App\Filament\Resources\Rental\RentalContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRentalContract extends ViewRecord
{
    protected static string $resource = RentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
