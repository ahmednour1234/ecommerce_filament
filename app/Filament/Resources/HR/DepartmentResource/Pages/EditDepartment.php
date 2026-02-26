<?php

namespace App\Filament\Resources\HR\DepartmentResource\Pages;

use App\Filament\Resources\HR\DepartmentResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditDepartment extends BaseEditRecord
{
    protected static string $resource = DepartmentResource::class;

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

