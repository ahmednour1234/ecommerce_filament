<?php

namespace App\Filament\Resources\Recruitment\LaborerResource\Pages;

use App\Filament\Resources\Recruitment\LaborerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLaborer extends ViewRecord
{
    protected static string $resource = LaborerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(tr('general.actions.edit', [], null, 'dashboard') ?: 'Edit'),
        ];
    }
}
