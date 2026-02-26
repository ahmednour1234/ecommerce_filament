<?php

namespace App\Filament\Resources\Housing\BuildingResource\Pages;

use App\Filament\Resources\Housing\BuildingResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditBuilding extends BaseEditRecord
{
    protected static string $resource = BuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
