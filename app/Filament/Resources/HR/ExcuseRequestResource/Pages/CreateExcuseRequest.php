<?php

namespace App\Filament\Resources\HR\ExcuseRequestResource\Pages;

use App\Filament\Resources\HR\ExcuseRequestResource;
use App\Services\HR\ExcuseRequestService;
use App\Filament\Pages\BaseCreateRecord;

class CreateExcuseRequest extends BaseCreateRecord
{
    protected static string $resource = ExcuseRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate end_time
        $startTime = \Carbon\Carbon::parse($data['start_time']);
        $endTime = $startTime->copy()->addHours($data['hours']);
        $data['end_time'] = $endTime->format('H:i:s');
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

