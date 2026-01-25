<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClient extends EditRecord
{
    protected static string $resource = ClientResource::class;

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
