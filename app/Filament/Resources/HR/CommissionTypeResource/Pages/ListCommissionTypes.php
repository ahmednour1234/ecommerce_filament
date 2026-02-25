<?php

namespace App\Filament\Resources\HR\CommissionTypeResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\CommissionTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommissionTypes extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = CommissionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.create_commission_type', [], null, 'dashboard') ?: 'Create Commission Type')
                ->visible(fn () => auth()->user()?->can('hr_commission_types.create') ?? false),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => auth()->user()?->can('hr_commission_types.view_any') ?? false),
        ];
    }
}
