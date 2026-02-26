<?php

namespace App\Filament\Resources\Accounting\FiscalYearResource\Pages;

use App\Filament\Resources\Accounting\FiscalYearResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditFiscalYear extends BaseEditRecord
{
    protected static string $resource = FiscalYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

