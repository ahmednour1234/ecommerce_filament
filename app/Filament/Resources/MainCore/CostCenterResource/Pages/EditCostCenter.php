<?php

namespace App\Filament\Resources\MainCore\CostCenterResource\Pages;

use App\Filament\Resources\MainCore\CostCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCostCenter extends EditRecord
{
    protected static string $resource = CostCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

