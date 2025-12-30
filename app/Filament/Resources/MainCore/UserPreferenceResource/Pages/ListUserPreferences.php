<?php

namespace App\Filament\Resources\MainCore\UserPreferenceResource\Pages;

use App\Filament\Resources\MainCore\UserPreferenceResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserPreferences extends ListRecords
{
    use ExportsResourceTable;
    protected static string $resource = UserPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

