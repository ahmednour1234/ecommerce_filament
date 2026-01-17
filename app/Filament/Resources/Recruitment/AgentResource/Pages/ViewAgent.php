<?php

namespace App\Filament\Resources\Recruitment\AgentResource\Pages;

use App\Filament\Resources\Recruitment\AgentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAgent extends ViewRecord
{
    protected static string $resource = AgentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(tr('actions.edit', [], null, 'dashboard') ?: 'Edit'),
        ];
    }
}
