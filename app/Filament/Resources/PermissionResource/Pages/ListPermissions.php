<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermissions extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.create', [], null, 'dashboard') ?: 'Create'),
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_to_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_to_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf()),

            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }
}
