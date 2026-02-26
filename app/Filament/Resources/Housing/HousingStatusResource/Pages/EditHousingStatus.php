<?php

namespace App\Filament\Resources\Housing\HousingStatusResource\Pages;

use App\Filament\Resources\Housing\HousingStatusResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditHousingStatus extends BaseEditRecord
{
    protected static string $resource = HousingStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
