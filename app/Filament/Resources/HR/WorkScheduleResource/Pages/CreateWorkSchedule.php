<?php

namespace App\Filament\Resources\HR\WorkScheduleResource\Pages;

use App\Filament\Resources\HR\WorkScheduleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkSchedule extends CreateRecord
{
    protected static string $resource = WorkScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

