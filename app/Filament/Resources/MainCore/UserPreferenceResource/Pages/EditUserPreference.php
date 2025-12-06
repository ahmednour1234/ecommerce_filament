<?php

namespace App\Filament\Resources\MainCore\UserPreferenceResource\Pages;

use App\Filament\Resources\MainCore\UserPreferenceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserPreference extends EditRecord
{
    protected static string $resource = UserPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

