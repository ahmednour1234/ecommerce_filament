<?php

namespace App\Filament\Resources\Recruitment\ProfessionResource\Pages;

use App\Filament\Resources\Recruitment\ProfessionResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditProfession extends BaseEditRecord
{
    protected static string $resource = ProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
