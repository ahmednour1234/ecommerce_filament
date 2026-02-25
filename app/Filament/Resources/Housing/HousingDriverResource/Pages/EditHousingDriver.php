<?php

namespace App\Filament\Resources\Housing\HousingDriverResource\Pages;

use App\Filament\Resources\Housing\HousingDriverResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHousingDriver extends EditRecord
{
    protected static string $resource = HousingDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
