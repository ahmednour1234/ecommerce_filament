<?php

namespace App\Filament\Resources\Accounting\AssetResource\Pages;

use App\Filament\Resources\Accounting\AssetResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditAsset extends BaseEditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

