<?php

namespace App\Filament\Resources\MainCore\ShipmentResource\Pages;

use App\Filament\Resources\MainCore\ShipmentResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditShipment extends BaseEditRecord
{
    protected static string $resource = ShipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

