<?php

namespace App\Filament\Resources\Recruitment\AgentLaborPriceResource\Pages;

use App\Filament\Resources\Recruitment\AgentLaborPriceResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditAgentLaborPrice extends BaseEditRecord
{
    protected static string $resource = AgentLaborPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
