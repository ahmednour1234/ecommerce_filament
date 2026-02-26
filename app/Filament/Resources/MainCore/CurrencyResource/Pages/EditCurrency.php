<?php

namespace App\Filament\Resources\MainCore\CurrencyResource\Pages;

use App\Filament\Resources\MainCore\CurrencyResource;
use App\Filament\Pages\BaseEditRecord;
use App\Filament\Actions\DeleteAction;

class EditCurrency extends BaseEditRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
