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
}

