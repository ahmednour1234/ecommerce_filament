<?php

namespace App\Filament\Resources\Housing\HousingDriverResource\Pages;

use App\Filament\Resources\Housing\HousingDriverResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditHousingDriver extends BaseEditRecord
{
    protected static string $resource = HousingDriverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
