<?php

namespace App\Filament\Resources\Recruitment\NationalityResource\Pages;

use App\Filament\Resources\Recruitment\NationalityResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditNationality extends BaseEditRecord
{
    protected static string $resource = NationalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
