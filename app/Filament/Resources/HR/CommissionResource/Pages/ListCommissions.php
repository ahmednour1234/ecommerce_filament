<?php

namespace App\Filament\Resources\HR\CommissionResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\CommissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommissions extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = CommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.create_commission', [], null, 'dashboard') ?: 'Create Commission')
                ->visible(fn () => auth()->user()?->can('hr_commissions.create') ?? false),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => auth()->user()?->can('hr_commissions.view_any') ?? false),
        ];
    }
}
