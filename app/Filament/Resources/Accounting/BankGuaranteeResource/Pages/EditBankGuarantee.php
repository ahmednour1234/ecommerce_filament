<?php

namespace App\Filament\Resources\Accounting\BankGuaranteeResource\Pages;

use App\Filament\Resources\Accounting\BankGuaranteeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class EditBankGuarantee extends EditRecord
{
    protected static string $resource = BankGuaranteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validate bank_fees_account_id is required when bank_fees > 0
        if (isset($data['bank_fees']) && (float) $data['bank_fees'] > 0) {
            if (empty($data['bank_fees_account_id'])) {
                throw ValidationException::withMessages([
                    'bank_fees_account_id' => tr('validation.bank_fees_account_required', [], null, 'dashboard'),
                ]);
            }
        }

        // Ensure end_date is after start_date
        if (isset($data['start_date']) && isset($data['end_date'])) {
            if ($data['end_date'] <= $data['start_date']) {
                throw ValidationException::withMessages([
                    'end_date' => tr('validation.end_date_after_start_date', [], null, 'dashboard'),
                ]);
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(tr('messages.bank_guarantees.updated', [], null, 'dashboard'))
            ->body(tr('messages.bank_guarantees.updated_successfully', [], null, 'dashboard'));
    }
}

