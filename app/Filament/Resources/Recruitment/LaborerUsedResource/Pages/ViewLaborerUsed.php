<?php

namespace App\Filament\Resources\Recruitment\LaborerUsedResource\Pages;

use App\Filament\Resources\Recruitment\LaborerUsedResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLaborerUsed extends ViewRecord
{
    protected static string $resource = LaborerUsedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(tr('general.actions.delete', [], null, 'dashboard') ?: 'Delete'),
        ];
    }
}
