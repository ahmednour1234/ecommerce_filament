<?php

namespace App\Filament\Resources\Packages\PackageResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Packages\PackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPackages extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('common.create', [], null, 'dashboard')),
            Actions\Action::make('export_excel')
                ->label(tr('general.actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel())
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('packages.export_pdf') ?? false),
            Actions\Action::make('export_pdf')
                ->label(tr('general.actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf())
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('packages.export_pdf') ?? false),
            Actions\Action::make('print')
                ->label(tr('general.actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('packages.export_pdf') ?? false),
        ];
    }
}
