<?php

namespace App\Filament\Resources\HR\EmployeeGroupResource\Pages;

use App\Filament\Resources\HR\EmployeeGroupResource;
use App\Services\HR\EmployeeGroupService;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditEmployeeGroup extends BaseEditRecord
{
    protected static string $resource = EmployeeGroupResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $members = $data['members'] ?? [];
        unset($data['members']);
        $this->members = $members;
        return $data;
    }

    protected function afterSave(): void
    {
        if (isset($this->members)) {
            app(EmployeeGroupService::class)->syncMembers($this->record, $this->members);
        }
    }

    protected function fillForm(): void
    {
        $this->form->fill([
            ...$this->record->attributesToArray(),
            'members' => $this->record->employees->pluck('id')->toArray(),
        ]);
    }

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

