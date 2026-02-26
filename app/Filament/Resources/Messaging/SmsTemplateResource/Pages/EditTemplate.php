<?php

namespace App\Filament\Resources\Messaging\SmsTemplateResource\Pages;

use App\Filament\Resources\Messaging\SmsTemplateResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditTemplate extends BaseEditRecord
{
    protected static string $resource = SmsTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
