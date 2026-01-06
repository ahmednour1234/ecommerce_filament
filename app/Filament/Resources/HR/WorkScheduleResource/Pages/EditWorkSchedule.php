<?php

namespace App\Filament\Resources\HR\WorkScheduleResource\Pages;

use App\Filament\Resources\HR\WorkScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkSchedule extends EditRecord
{
    protected static string $resource = WorkScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

