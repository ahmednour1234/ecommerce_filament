<?php

namespace App\Filament\Resources\Accounting\AccountResource\Pages;

use App\Filament\Resources\Accounting\AccountResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditAccount extends BaseEditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

