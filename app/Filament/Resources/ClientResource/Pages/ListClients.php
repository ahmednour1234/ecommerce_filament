<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('general.clients.add_client', [], null, 'dashboard') ?: 'Add Client'),
        ];
    }
}
