<?php

namespace App\Filament\Resources\HR\HolidayResource\Pages;

use App\Filament\Resources\HR\HolidayResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateHoliday extends BaseCreateRecord
{
    protected static string $resource = HolidayResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

