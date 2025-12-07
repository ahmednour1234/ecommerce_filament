<?php

namespace App\Filament\Resources\Sales\InstallmentResource\Pages;

use App\Filament\Resources\Sales\InstallmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstallments extends ListRecords
{
    protected static string $resource = InstallmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

