<?php

namespace App\Filament\Resources\MainCore\CostCenterResource\Pages;

use App\Filament\Resources\MainCore\CostCenterResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditCostCenter extends BaseEditRecord
{
    protected static string $resource = CostCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

