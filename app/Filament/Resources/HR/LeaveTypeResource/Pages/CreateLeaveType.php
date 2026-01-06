<?php

namespace App\Filament\Resources\HR\LeaveTypeResource\Pages;

use App\Filament\Resources\HR\LeaveTypeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Services\HR\LeaveTypeService;

class CreateLeaveType extends CreateRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

