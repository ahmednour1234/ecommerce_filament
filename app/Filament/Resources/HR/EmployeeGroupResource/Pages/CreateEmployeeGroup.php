<?php

namespace App\Filament\Resources\HR\EmployeeGroupResource\Pages;

use App\Filament\Resources\HR\EmployeeGroupResource;
use App\Services\HR\EmployeeGroupService;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeGroup extends CreateRecord
{
    protected static string $resource = EmployeeGroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $members = $data['members'] ?? [];
        unset($data['members']);
        $this->members = $members;
        return $data;
    }

    protected function afterCreate(): void
    {
        if (isset($this->members)) {
            app(EmployeeGroupService::class)->syncMembers($this->record, $this->members);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

