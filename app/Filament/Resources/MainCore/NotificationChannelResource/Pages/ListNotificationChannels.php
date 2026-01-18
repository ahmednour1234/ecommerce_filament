<?php

namespace App\Filament\Resources\MainCore\NotificationChannelResource\Pages;

use App\Filament\Resources\MainCore\NotificationChannelResource;
use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Concerns\NotificationModuleGate;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListNotificationChannels extends ListRecords
{
    use ExportsResourceTable,NotificationModuleGate;
    protected static string $resource = NotificationChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

