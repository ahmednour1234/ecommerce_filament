<?php

namespace App\Filament\Resources\HR\BloodTypeResource\Pages;

use App\Filament\Resources\HR\BloodTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBloodType extends EditRecord
{
    protected static string $resource = BloodTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

