<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use App\Filament\Pages\BaseCreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRole extends BaseCreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        $role = static::getModel()::create($data);
        
        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return $role;
    }
}
