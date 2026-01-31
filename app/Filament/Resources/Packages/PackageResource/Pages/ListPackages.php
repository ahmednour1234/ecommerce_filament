<?php

namespace App\Filament\Resources\Packages\PackageResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Packages\PackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackages extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('common.create', [], null, 'dashboard')),
        ];
    }
}
