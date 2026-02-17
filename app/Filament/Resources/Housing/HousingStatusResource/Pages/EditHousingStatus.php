<?php

namespace App\Filament\Resources\Housing\HousingStatusResource\Pages;

use App\Filament\Resources\Housing\HousingStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHousingStatus extends EditRecord
{
    protected static string $resource = HousingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
