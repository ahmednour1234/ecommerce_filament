<?php

namespace App\Filament\Resources\Recruitment\AgentLaborPriceResource\Pages;

use App\Filament\Resources\Recruitment\AgentLaborPriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentLaborPrices extends ListRecords
{
    protected static string $resource = AgentLaborPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
