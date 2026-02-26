<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends BaseEditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remove password from data when loading the form (don't show hashed password)
        unset($data['password']);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If password is empty or null, remove it from data so it won't be updated
        if (empty($data['password']) || is_null($data['password'])) {
            unset($data['password']);
        } else {
            // Only hash if it's not already hashed (check if it starts with $2y$ which is bcrypt format)
            $password = trim($data['password']);
            if (!str_starts_with($password, '$2y$') && !str_starts_with($password, '$2a$') && !str_starts_with($password, '$2b$')) {
                // Hash the password before saving
                $data['password'] = Hash::make($password);
            }
        }

        return $data;
    }
}
