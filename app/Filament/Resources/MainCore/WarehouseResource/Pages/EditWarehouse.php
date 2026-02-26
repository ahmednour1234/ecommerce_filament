<?php

namespace App\Filament\Resources\MainCore\WarehouseResource\Pages;

use App\Filament\Resources\MainCore\WarehouseResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditWarehouse extends BaseEditRecord
{
    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

