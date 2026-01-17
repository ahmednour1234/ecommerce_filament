<?php

namespace App\Filament\Resources\Recruitment\ProfessionResource\Pages;

use App\Filament\Resources\Recruitment\ProfessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProfession extends ViewRecord
{
    protected static string $resource = ProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(tr('actions.edit', [], null, 'dashboard') ?: 'Edit'),
        ];
    }
}
