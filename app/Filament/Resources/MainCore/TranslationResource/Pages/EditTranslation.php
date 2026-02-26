<?php

namespace App\Filament\Resources\MainCore\TranslationResource\Pages;

use App\Filament\Resources\MainCore\TranslationResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditTranslation extends BaseEditRecord
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

