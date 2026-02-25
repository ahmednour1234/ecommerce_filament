<?php

namespace App\Filament\Resources\Messaging\SmsTemplateResource\Pages;

use App\Filament\Resources\Messaging\SmsTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplates extends ListRecords
{
    protected static string $resource = SmsTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
