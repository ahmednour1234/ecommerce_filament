<?php

namespace App\Filament\Resources\Sales\InstallmentResource\Pages;

use App\Filament\Resources\Sales\InstallmentResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditInstallment extends BaseEditRecord
{
    protected static string $resource = InstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

