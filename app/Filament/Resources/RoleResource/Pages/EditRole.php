<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditRole extends BaseEditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormData(array $data): array
    {
        if ($this->record && $this->record->exists) {
            $data['permissions'] = $this->record->permissions->pluck('id')->toArray();
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $this->record->syncPermissions($permissions);

        return $data;
    }
}
