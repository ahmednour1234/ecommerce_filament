<?php

namespace App\Filament\Resources\MainCore\ShippingProviderResource\Pages;

use App\Filament\Resources\MainCore\ShippingProviderResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingProviders extends ListRecords
{
    use ExportsResourceTable;
    protected static string $resource = ShippingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

