<?php

namespace App\Filament\Resources\MainCore\LanguageResource\Pages;

use App\Filament\Resources\MainCore\LanguageResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditLanguage extends BaseEditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
