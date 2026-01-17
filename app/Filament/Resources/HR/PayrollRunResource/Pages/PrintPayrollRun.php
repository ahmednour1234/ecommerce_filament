<?php

namespace App\Filament\Resources\HR\PayrollRunResource\Pages;

use App\Filament\Resources\HR\PayrollRunResource;
use App\Models\HR\PayrollRun;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class PrintPayrollRun extends Page
{
    use InteractsWithRecord;

    protected static string $resource = PayrollRunResource::class;

    protected static string $view = 'filament.hr.payroll.payroll-sheet';

    public function mount(PayrollRun $record): void
    {
        abort_unless(auth()->user()?->can('hr_payroll.export'), 403);

        $this->record = $record->load([
            'department',
            'items.employee.department',
            'items.lines.component',
        ]);
    }
}
