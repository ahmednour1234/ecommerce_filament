<?php

namespace App\Filament\Resources\HR\CommissionResource\Pages;

use App\Filament\Resources\HR\CommissionResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditCommission extends BaseEditRecord
{
    protected static string $resource = CommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commissions.restore') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
        ];
    }
}
