<?php

namespace App\Filament\Resources\MainCore\NotificationTemplateResource\Pages;

use App\Filament\Resources\MainCore\NotificationTemplateResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditNotificationTemplate extends BaseEditRecord
{
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

