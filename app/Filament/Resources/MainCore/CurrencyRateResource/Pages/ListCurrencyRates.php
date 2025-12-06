<?php

namespace App\Filament\Resources\MainCore\CurrencyRateResource\Pages;

use App\Filament\Resources\MainCore\CurrencyRateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurrencyRates extends ListRecords
{
    protected static string $resource = CurrencyRateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
