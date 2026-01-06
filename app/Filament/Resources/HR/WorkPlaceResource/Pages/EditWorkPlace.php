<?php

namespace App\Filament\Resources\HR\WorkPlaceResource\Pages;

use App\Filament\Resources\HR\WorkPlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkPlace extends EditRecord
{
    protected static string $resource = WorkPlaceResource::class;

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

