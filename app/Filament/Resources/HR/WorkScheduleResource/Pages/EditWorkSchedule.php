<?php

namespace App\Filament\Resources\HR\WorkScheduleResource\Pages;

use App\Filament\Resources\HR\WorkScheduleResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditWorkSchedule extends BaseEditRecord
{
    protected static string $resource = WorkScheduleResource::class;

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

