<?php

namespace App\Filament\Resources\Accounting\PeriodResource\Pages;

use App\Filament\Resources\Accounting\PeriodResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditPeriod extends BaseEditRecord
{
    protected static string $resource = PeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

