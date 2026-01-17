<?php

namespace App\Filament\Resources\Recruitment\LaborerUsedResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Recruitment\LaborerUsedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaborersUsed extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = LaborerUsedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('general.actions.add', [], null, 'dashboard') ?: 'Add')
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('recruitment.laborers_used.create') ?? false),
            Actions\Action::make('export_excel')
                ->label(tr('general.actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('export_pdf')
                ->label(tr('general.actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf()),

            Actions\Action::make('print')
                ->label(tr('general.actions.print', [], null, 'dashboard') ?: 'Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }
}
