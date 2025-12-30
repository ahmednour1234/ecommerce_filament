<?php

namespace App\Filament\Resources\MainCore\NotificationTemplateResource\Pages;

use App\Filament\Resources\MainCore\NotificationTemplateResource;
use App\Filament\Concerns\ExportsResourceTable;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotificationTemplates extends ListRecords
{
    use ExportsResourceTable;
    protected static string $resource = NotificationTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

