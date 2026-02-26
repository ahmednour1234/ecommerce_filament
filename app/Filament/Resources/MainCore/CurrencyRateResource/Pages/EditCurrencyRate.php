<?php

namespace App\Filament\Resources\MainCore\CurrencyRateResource\Pages;

use App\Filament\Resources\MainCore\CurrencyRateResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditCurrencyRate extends BaseEditRecord
{
    protected static string $resource = CurrencyRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
