<?php

namespace App\Filament\Resources\HR\EmployeeCommissionTierResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\EmployeeCommissionTierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeCommissionTiers extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = EmployeeCommissionTierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.create_tier', [], null, 'dashboard') ?: 'Create Tier')
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.create') ?? false),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => auth()->user()?->can('hr_employee_commission_tiers.view_any') ?? false),
        ];
    }
}
