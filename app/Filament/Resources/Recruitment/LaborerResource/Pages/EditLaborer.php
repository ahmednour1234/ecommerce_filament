<?php

namespace App\Filament\Resources\Recruitment\LaborerResource\Pages;

use App\Filament\Resources\Recruitment\LaborerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaborer extends EditRecord
{
    protected static string $resource = LaborerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(tr('general.actions.details', [], null, 'dashboard') ?: 'Details'),
            Actions\DeleteAction::make()
                ->label(tr('general.actions.delete', [], null, 'dashboard') ?: 'Delete'),
        ];
    }
}
