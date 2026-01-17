<?php

namespace App\Filament\Resources\Recruitment\AgentLaborPriceResource\Pages;

use App\Filament\Resources\Recruitment\AgentLaborPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAgentLaborPrice extends ViewRecord
{
    protected static string $resource = AgentLaborPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(tr('actions.edit', [], null, 'dashboard') ?: 'Edit'),
        ];
    }
}
