<?php

namespace App\Filament\Resources\Housing\HousingRequestResource\Pages;

use App\Filament\Resources\Housing\HousingRequestResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditHousingRequest extends BaseEditRecord
{
    protected static string $resource = HousingRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
