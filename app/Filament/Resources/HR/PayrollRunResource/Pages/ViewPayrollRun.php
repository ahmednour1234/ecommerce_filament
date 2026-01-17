<?php

namespace App\Filament\Resources\HR\PayrollRunResource\Pages;

use App\Filament\Resources\HR\PayrollRunResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewPayrollRun extends ViewRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(trans_dash('actions.print_payroll') ?: 'Print Payroll Sheet')
                ->icon('heroicon-o-printer')
                ->url(fn () => PayrollRunResource::getUrl('print', ['record' => $this->record]))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()?->can('hr_payroll.export') ?? false),
        ];
    }
}
