<?php

namespace App\Filament\Resources\Accounting\ProjectResource\Pages;

use App\Filament\Resources\Accounting\ProjectResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditProject extends BaseEditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

