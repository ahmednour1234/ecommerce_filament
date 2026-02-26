<?php

namespace App\Filament\Resources\Messaging\MessageContactResource\Pages;

use App\Filament\Resources\Messaging\MessageContactResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditContact extends BaseEditRecord
{
    protected static string $resource = MessageContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
