<?php

namespace App\Filament\Resources\HR\EmployeeResource\Pages;

use App\Filament\Resources\HR\EmployeeResource;
use App\Filament\Pages\BaseCreateRecord;
use Filament\Notifications\Notification;

class CreateEmployee extends BaseCreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(tr('messages.employee_created', [], null, 'dashboard') ?: 'Employee created successfully')
            ->body(tr('messages.employee_created', [], null, 'dashboard') ?: 'The employee has been created successfully.');
    }
}

