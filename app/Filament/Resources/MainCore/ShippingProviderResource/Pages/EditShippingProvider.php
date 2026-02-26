<?php

namespace App\Filament\Resources\MainCore\ShippingProviderResource\Pages;

use App\Filament\Resources\MainCore\ShippingProviderResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditShippingProvider extends BaseEditRecord
{
    protected static string $resource = ShippingProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

