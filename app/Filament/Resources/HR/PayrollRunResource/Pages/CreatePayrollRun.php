<?php

namespace App\Filament\Resources\HR\PayrollRunResource\Pages;

use App\Filament\Resources\HR\PayrollRunResource;
use App\Services\HR\PayrollGenerationService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePayrollRun extends CreateRecord
{
    protected static string $resource = PayrollRunResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $service = app(PayrollGenerationService::class);

        $payrollRun = $service->generatePayroll(
            $data['year'],
            $data['month'],
            $data['department_id'] ?? null,
            $data['include_attendance_deductions'] ?? true,
            $data['include_loan_installments'] ?? true,
            auth()->id()
        );

        Notification::make()
            ->title('Payroll generated successfully')
            ->success()
            ->send();

        $this->record = $payrollRun;

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
