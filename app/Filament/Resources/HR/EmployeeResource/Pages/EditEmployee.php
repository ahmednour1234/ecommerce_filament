<?php

namespace App\Filament\Resources\HR\EmployeeResource\Pages;

use App\Filament\Resources\HR\EmployeeResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;
use Filament\Notifications\Notification;

class EditEmployee extends BaseEditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_employees.delete') ?? false),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(tr('messages.employee_updated', [], null, 'dashboard') ?: 'Employee updated successfully')
            ->body(tr('messages.employee_updated', [], null, 'dashboard') ?: 'The employee has been updated successfully.');
    }
}

