<?php

namespace App\Filament\Resources\Housing\HousingRequestResource\Pages;

use App\Filament\Resources\Housing\HousingRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHousingRequest extends EditRecord
{
    protected static string $resource = HousingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
