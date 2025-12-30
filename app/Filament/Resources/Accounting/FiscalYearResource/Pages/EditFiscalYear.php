<?php

namespace App\Filament\Resources\Accounting\FiscalYearResource\Pages;

use App\Filament\Resources\Accounting\FiscalYearResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFiscalYear extends EditRecord
{
    protected static string $resource = FiscalYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

