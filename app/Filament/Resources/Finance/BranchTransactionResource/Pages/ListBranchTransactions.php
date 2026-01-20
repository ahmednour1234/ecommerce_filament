<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\Finance\BranchTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchTransactions extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = BranchTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.create_transactions') ?? false),

            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard') ?: 'Export to Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard') ?: 'Export to PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(fn () => $this->exportToPdf()),



            Actions\Action::make('print_pdf')
                ->label(tr('actions.print_pdf', [], null, 'dashboard') ?: 'Print PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->action(function () {
                    $response = $this->exportToPdf();
                    $response->headers->set('Content-Disposition', 'inline; filename="' . $this->getExportFilename('pdf') . '"');
                    return $response;
                })
                ->color('gray'),
        ];
    }
}
