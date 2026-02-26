<?php

namespace App\Filament\Resources\HR\WorkScheduleResource\Pages;

use App\Filament\Resources\HR\WorkScheduleResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateWorkSchedule extends BaseCreateRecord
{
    protected static string $resource = WorkScheduleResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

