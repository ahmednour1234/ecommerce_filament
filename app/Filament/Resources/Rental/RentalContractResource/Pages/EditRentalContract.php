<?php

namespace App\Filament\Resources\Rental\RentalContractResource\Pages;

use App\Filament\Resources\Rental\RentalContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalContract extends EditRecord
{
    protected static string $resource = RentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
