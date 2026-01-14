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
            Actions\CreateAction::make()->visible(fn()=> auth()->user()?->can('branch_tx.create')),
            Actions\Action::make('export_excel')
                ->label('Excel')
                ->visible(fn()=> auth()->user()?->can('branch_tx.export'))
                ->action(fn()=> $this->exportToExcel()),
            Actions\Action::make('export_pdf')
                ->label('PDF')
                ->visible(fn()=> auth()->user()?->can('branch_tx.export'))
                ->action(fn()=> $this->exportToPdf()),
            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard'))
                ->visible(fn()=> auth()->user()?->can('branch_tx.print'))
                ->url(fn()=> $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getExportTitle(): ?string
    {
        return tr('reports.branch_tx.title', [], null, 'dashboard');
    }
}
