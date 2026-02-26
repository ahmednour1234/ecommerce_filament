<?php

namespace App\Filament\Resources\HR\DepartmentResource\Pages;

use App\Filament\Resources\HR\DepartmentResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateDepartment extends BaseCreateRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

