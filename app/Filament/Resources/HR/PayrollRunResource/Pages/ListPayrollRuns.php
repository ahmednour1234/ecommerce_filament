<?php

namespace App\Filament\Resources\HR\PayrollRunResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\PayrollRunResource;
use App\Services\HR\PayrollGenerationService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Notifications\Notification;

class ListPayrollRuns extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = PayrollRunResource::class;

    public function getHeaderActions(): array
    {
        $actions = [
            Actions\CreateAction::make()
                ->label(trans_dash('forms.payroll.create_payroll') ?: 'Create Payroll Sheet')
                ->visible(fn () => auth()->user()?->can('hr_payroll.create') ?? false),
        ];

        $actions[] = Actions\Action::make('export_excel')
            ->label(trans_dash('actions.export_excel') ?: 'Export Excel')
            ->icon('heroicon-o-arrow-down-tray')
            ->action(fn () => $this->exportToExcel())
            ->visible(fn () => auth()->user()?->can('hr_payroll.export') ?? false);

        $actions[] = Actions\Action::make('export_pdf')
            ->label(trans_dash('actions.export_pdf') ?: 'Export PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->action(fn () => $this->exportToPdf())
            ->visible(fn () => auth()->user()?->can('hr_payroll.export') ?? false);

        return $actions;
    }

    public function getTableBulkActions(): array
    {
        return [
            Tables\Actions\BulkAction::make('approve_all')
                ->label(trans_dash('actions.approve_all') ?: 'Approve All')
                ->icon('heroicon-o-check-circle')
                ->action(function ($records) {
                    $service = app(PayrollGenerationService::class);
                    foreach ($records as $record) {
                        $service->approveAll($record);
                    }
                    Notification::make()
                        ->title('Payroll runs approved')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->visible(fn () => auth()->user()?->can('hr_payroll.approve') ?? false)
                ->deselectRecordsAfterCompletion(),

            Tables\Actions\BulkAction::make('pay_all')
                ->label(trans_dash('actions.pay_all') ?: 'Pay All')
                ->icon('heroicon-o-banknotes')
                ->action(function ($records) {
                    $service = app(PayrollGenerationService::class);
                    foreach ($records as $record) {
                        $service->payAll($record);
                    }
                    Notification::make()
                        ->title('Payroll runs marked as paid')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->visible(fn () => auth()->user()?->can('hr_payroll.pay') ?? false)
                ->deselectRecordsAfterCompletion(),
        ];
    }
}
