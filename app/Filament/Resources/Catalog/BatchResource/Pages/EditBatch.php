<?php

namespace App\Filament\Resources\Catalog\BatchResource\Pages;

use App\Filament\Resources\Catalog\BatchResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditBatch extends BaseEditRecord
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

