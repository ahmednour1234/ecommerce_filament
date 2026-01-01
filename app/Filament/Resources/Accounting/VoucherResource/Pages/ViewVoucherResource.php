<?php

namespace App\Filament\Resources\Accounting\VoucherResource\Pages;

use App\Filament\Resources\Accounting\VoucherResource;
use App\Models\Accounting\Voucher;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVoucher extends ViewRecord
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Voucher $record */
        $record = $this->record;

        return [
            Actions\Action::make('print')
                ->label(trans_dash('vouchers.actions.print_voucher', 'Print Voucher'))
                ->icon('heroicon-o-printer')
                ->color('info')
                ->form(fn () => VoucherResource::signaturePickerForm($record))
                ->action(function (array $data) use ($record) {
                    $signatureIds = VoucherResource::extractSignatureIdsOrFail($data);
                    return app(\App\Services\Accounting\VoucherPrintService::class)->streamPdf($record, $signatureIds);
                }),

            Actions\Action::make('pdf')
                ->label(trans_dash('vouchers.actions.export_pdf', 'Export PDF'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form(fn () => VoucherResource::signaturePickerForm($record))
                ->action(function (array $data) use ($record) {
                    $signatureIds = VoucherResource::extractSignatureIdsOrFail($data);
                    return app(\App\Services\Accounting\VoucherPrintService::class)->downloadPdf($record, $signatureIds);
                }),

            Actions\Action::make('excel')
                ->label(trans_dash('vouchers.actions.export_excel', 'Export Excel'))
                ->icon('heroicon-o-table-cells')
                ->color('warning')
                ->action(fn () => app(\App\Services\Accounting\VoucherPrintService::class)->downloadCsv($record)),

            Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->can('vouchers.update') ?? false),
        ];
    }
}
