<?php

namespace App\Filament\Resources\Accounting\JournalResource\Pages;

use App\Filament\Resources\Accounting\JournalResource;
use App\Models\Accounting\VoucherSignature;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;

class ViewJournal extends ViewRecord
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->can('journals.update') ?? false),

            $this->exportPdfAction(),
            $this->printAction(),
            $this->exportExcelAction(),
        ];
    }

    protected function exportPdfAction(): Actions\Action
    {
        return Actions\Action::make('export_pdf')
            ->label(trans_dash('common.export_pdf', 'Export PDF'))
            ->icon('heroicon-o-document-arrow-down')
            ->form($this->signatureForm())
            ->action(function (array $data) {
                $signatures = $this->loadSignatures($data);

                // TODO: generate pdf (code below)
                return response()->streamDownload(function () use ($signatures) {
                    echo app(\App\Services\Accounting\JournalPrintService::class)
                        ->pdf($this->record, $signatures)
                        ->output();
                }, "journal-{$this->record->code}.pdf");
            });
    }

    protected function printAction(): Actions\Action
    {
        return Actions\Action::make('print')
            ->label(trans_dash('common.print', 'Print'))
            ->icon('heroicon-o-printer')
            ->form($this->signatureForm())
            ->action(function (array $data) {
                $signatures = $this->loadSignatures($data);

                $html = app(\App\Services\Accounting\JournalPrintService::class)
                    ->html($this->record, $signatures);

                // يفتح صفحة للطباعة
                return response($html);
            });
    }

    protected function exportExcelAction(): Actions\Action
    {
        return Actions\Action::make('export_excel')
            ->label(trans_dash('common.export_excel', 'Export Excel'))
            ->icon('heroicon-o-table-cells')
            ->form($this->signatureForm())
            ->action(function (array $data) {
                $signatures = $this->loadSignatures($data);

                // TODO: generate excel (code below)
                return app(\App\Services\Accounting\JournalPrintService::class)
                    ->excelDownload($this->record, $signatures);
            });
    }

    protected function signatureForm(): array
    {
        return [
            Forms\Components\Select::make('signature_ids')
                ->label(trans_dash('vouchers.signatures.select', 'Select Signatures'))
                ->multiple()
                ->searchable()
                ->preload()
                ->options(
                    VoucherSignature::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->pluck('name', 'id')
                        ->toArray()
                )
                ->helperText(trans_dash('vouchers.signatures.select_helper', 'Choose which signatures to appear on the document.')),
        ];
    }

    protected function loadSignatures(array $data): Collection
    {
        $ids = collect($data['signature_ids'] ?? [])->filter()->values();

        return VoucherSignature::query()
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
