<?php

namespace App\Filament\Resources\Messaging\MessageContactResource\Pages;

use App\Filament\Resources\Messaging\MessageContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContact extends EditRecord
{
    protected static string $resource = MessageContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
