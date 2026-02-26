<?php

namespace App\Filament\Resources\MainCore\UserPreferenceResource\Pages;

use App\Filament\Resources\MainCore\UserPreferenceResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditUserPreference extends BaseEditRecord
{
    protected static string $resource = UserPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

