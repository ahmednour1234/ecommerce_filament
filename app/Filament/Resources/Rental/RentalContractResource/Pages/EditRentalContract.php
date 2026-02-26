<?php

namespace App\Filament\Resources\Rental\RentalContractResource\Pages;

use App\Filament\Resources\Rental\RentalContractResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRentalContract extends BaseEditRecord
{
    protected static string $resource = RentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
