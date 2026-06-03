<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Pages\Finance\ImportBranchTransactionsPage;
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

            Actions\Action::make('import')
                ->label(tr('actions.import_excel', [], null, 'dashboard') ?: 'استيراد من Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->url(fn () => ImportBranchTransactionsPage::getUrl())
                ->visible(fn () => auth()->user()?->hasRole('super_admin')
                    || auth()->user()?->can('finance.transactions.import')
                    || auth()->user()?->can('finance.create_transactions')),

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

    protected function getTableDataForExport(): array
    {
        $records = $this->getTable()
            ->getQuery()
            ->with(['branch', 'currency', 'financeType'])
            ->get();

        $headers = [
            $this->ensureUtf8(tr('tables.branch_transactions.trx_date', [], null, 'dashboard') ?: 'Date'),
            $this->ensureUtf8(tr('tables.branch_transactions.branch', [], null, 'dashboard') ?: 'Branch'),
            $this->ensureUtf8(tr('tables.branch_transactions.kind', [], null, 'dashboard') ?: 'Kind'),
            $this->ensureUtf8(tr('tables.branch_transactions.type', [], null, 'dashboard') ?: 'Type'),
            $this->ensureUtf8(tr('tables.branch_transactions.amount', [], null, 'dashboard') ?: 'Amount'),
            $this->ensureUtf8(tr('tables.branch_transactions.currency', [], null, 'dashboard') ?: 'Currency'),
            $this->ensureUtf8(tr('tables.branch_transactions.reference_no', [], null, 'dashboard') ?: 'Reference'),
            $this->ensureUtf8(tr('tables.branch_transactions.recipient_name', [], null, 'dashboard') ?: 'Recipient'),
            $this->ensureUtf8(tr('tables.branch_transactions.payment_method', [], null, 'dashboard') ?: 'Payment Method'),
            $this->ensureUtf8(tr('tables.branch_transactions.status', [], null, 'dashboard') ?: 'Status'),
        ];

        $data = $records->map(function ($record) use ($headers) {
            $kind = match ($record->financeType?->kind) {
                'income' => tr('forms.finance_types.kind_income', [], null, 'dashboard') ?: 'Income',
                'expense' => tr('forms.finance_types.kind_expense', [], null, 'dashboard') ?: 'Expense',
                default => '',
            };

            $status = tr('fields.status_' . $record->status, [], null, 'dashboard') ?: ucfirst((string) $record->status);

            return array_combine($headers, [
                $record->trx_date?->format('Y-m-d') ?? '',
                $this->ensureUtf8($record->branch?->name ?? ''),
                $this->ensureUtf8($kind),
                $this->ensureUtf8($record->financeType?->name_text ?? ''),
                number_format((float) $record->amount, 2),
                $this->ensureUtf8($record->currency?->code ?? ''),
                $this->ensureUtf8($record->reference_no ?? ''),
                $this->ensureUtf8($record->recipient_name ?? ''),
                $this->ensureUtf8($record->payment_method ?? ''),
                $this->ensureUtf8($status),
            ]);
        });

        return [
            'data' => $data,
            'headers' => $headers,
        ];
    }
}
