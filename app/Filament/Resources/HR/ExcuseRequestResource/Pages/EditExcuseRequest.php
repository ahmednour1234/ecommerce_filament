<?php

namespace App\Filament\Resources\HR\ExcuseRequestResource\Pages;

use App\Filament\Resources\HR\ExcuseRequestResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditExcuseRequest extends BaseEditRecord
{
    protected static string $resource = ExcuseRequestResource::class;

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

