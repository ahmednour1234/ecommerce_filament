<?php

namespace App\Filament\Resources\HR\CommissionResource\Pages;

use App\Filament\Resources\HR\CommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommission extends EditRecord
{
    protected static string $resource = CommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
            Actions\RestoreAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commissions.restore') ?? false),
            Actions\ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr_commissions.delete') ?? false),
        ];
    }
}
