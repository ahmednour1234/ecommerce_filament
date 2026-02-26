<?php

namespace App\Filament\Resources\MainCore\ThemeResource\Pages;

use App\Filament\Resources\MainCore\ThemeResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditTheme extends BaseEditRecord
{
    protected static string $resource = ThemeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
