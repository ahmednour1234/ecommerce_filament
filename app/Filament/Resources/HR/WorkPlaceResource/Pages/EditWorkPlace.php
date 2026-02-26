<?php

namespace App\Filament\Resources\HR\WorkPlaceResource\Pages;

use App\Filament\Resources\HR\WorkPlaceResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditWorkPlace extends BaseEditRecord
{
    protected static string $resource = WorkPlaceResource::class;

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

