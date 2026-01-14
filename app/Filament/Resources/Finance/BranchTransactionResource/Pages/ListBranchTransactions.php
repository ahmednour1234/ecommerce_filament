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
                ->visible(fn () => auth()->user()?->can('branch_tx.create') ?? false),

            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard'))
                ->visible(fn () => auth()->user()?->can('branch_tx.export') ?? false)
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard'))
                ->visible(fn () => auth()->user()?->can('branch_tx.export') ?? false)
                ->action(fn () => $this->exportToPdf()),

            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard'))
                ->visible(fn () => auth()->user()?->can('branch_tx.print') ?? false)
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getExportTitle(): ?string
    {
        return tr('reports.branch_tx.title', [], null, 'dashboard');
    }

    protected function getExportMetadata(): array
    {
        return [
            'exported_at' => now()->format('Y-m-d H:i:s'),
            'exported_by' => auth()->user()?->name ?? 'System',
        ];
    }
}
