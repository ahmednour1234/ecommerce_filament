<?php

namespace App\Filament\Resources\MainCore\ShippingProviderResource\Pages;

use App\Filament\Resources\MainCore\ShippingProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShippingProvider extends EditRecord
{
    protected static string $resource = ShippingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

