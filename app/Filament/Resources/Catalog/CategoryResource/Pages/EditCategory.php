<?php

namespace App\Filament\Resources\Catalog\CategoryResource\Pages;

use App\Filament\Resources\Catalog\CategoryResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditCategory extends BaseEditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

