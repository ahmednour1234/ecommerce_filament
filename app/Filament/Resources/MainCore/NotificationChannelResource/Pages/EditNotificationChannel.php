<?php

namespace App\Filament\Resources\MainCore\NotificationChannelResource\Pages;

use App\Filament\Resources\MainCore\NotificationChannelResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditNotificationChannel extends BaseEditRecord
{
    protected static string $resource = NotificationChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

