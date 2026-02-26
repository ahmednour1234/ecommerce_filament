<?php

namespace App\Filament\Resources\HR\IdentityTypeResource\Pages;

use App\Filament\Resources\HR\IdentityTypeResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditIdentityType extends BaseEditRecord
{
    protected static string $resource = IdentityTypeResource::class;

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

