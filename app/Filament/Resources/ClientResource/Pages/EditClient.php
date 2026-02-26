<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditClient extends BaseEditRecord
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(tr('general.actions.details', [], null, 'dashboard') ?: 'Details'),
            DeleteAction::make()
                ->label(tr('general.actions.delete', [], null, 'dashboard') ?: 'Delete'),
        ];
    }
}
