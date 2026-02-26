<?php

namespace App\Filament\Resources\HR\BloodTypeResource\Pages;

use App\Filament\Resources\HR\BloodTypeResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditBloodType extends BaseEditRecord
{
    protected static string $resource = BloodTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

