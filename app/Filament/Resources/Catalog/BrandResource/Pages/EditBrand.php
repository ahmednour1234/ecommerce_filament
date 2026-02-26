<?php

namespace App\Filament\Resources\Catalog\BrandResource\Pages;

use App\Filament\Resources\Catalog\BrandResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditBrand extends BaseEditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

