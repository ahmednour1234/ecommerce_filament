<?php

namespace App\Filament\Resources\Accounting\BankAccountResource\Pages;

use App\Filament\Resources\Accounting\BankAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EditBankAccount extends EditRecord
{
    protected static string $resource = BankAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Ensure relationships are loaded to prevent errors
        try {
            $this->record->loadMissing(['account', 'branch', 'currency']);
            
            // Recalculate current balance to ensure it's up to date
            $this->record->recalculateCurrentBalance();
            
            // Update data with recalculated balance
            $data['current_balance'] = $this->record->current_balance;
            
            // If account relationship is missing or invalid, handle gracefully
            if (!$this->record->account) {
                // Try to find a default asset account
                $defaultAccount = \App\Models\Accounting\Account::where('type', 'asset')
                    ->where('is_active', true)
                    ->first();
                
                if ($defaultAccount) {
                    $data['account_id'] = $defaultAccount->id;
                    \Filament\Notifications\Notification::make()
                        ->title('Warning: Missing Account')
                        ->body('The associated account was missing. A default account has been selected. Please verify.')
                        ->warning()
                        ->send();
                } else {
                    \Filament\Notifications\Notification::make()
                        ->title('Error: No Asset Accounts Found')
                        ->body('Please create an asset account first before editing this bank account.')
                        ->danger()
                        ->send();
                }
            }
        } catch (\Exception $e) {
            \Filament\Notifications\Notification::make()
                ->title('Error Loading Bank Account')
                ->body('There was an error loading the bank account data: ' . $e->getMessage())
                ->danger()
                ->send();
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If opening balance changed, recalculate current balance
        if (isset($data['opening_balance']) && $data['opening_balance'] != $this->record->opening_balance) {
            // The model's boot method will handle this, but we can also do it here
            $oldOpeningBalance = $this->record->opening_balance ?? 0;
            $newOpeningBalance = $data['opening_balance'] ?? 0;
            
            // If current balance was equal to old opening balance, update it to new opening balance
            if ($this->record->current_balance == $oldOpeningBalance) {
                $data['current_balance'] = $newOpeningBalance;
            } else {
                // Recalculate based on transactions
                $difference = $newOpeningBalance - $oldOpeningBalance;
                $data['current_balance'] = ($this->record->current_balance ?? 0) + $difference;
            }
        }
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Recalculate current balance after save to ensure accuracy
        $this->record->refresh();
        $this->record->recalculateCurrentBalance();
    }
}

