<?php

namespace App\Filament\Resources\Sales\OrderResource\Pages;

use App\Filament\Resources\Sales\OrderResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditOrder extends BaseEditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

