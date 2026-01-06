<?php

namespace App\Filament\Resources\Accounting\BankGuaranteeResource\Pages;

use App\Filament\Resources\Accounting\BankGuaranteeResource;
use App\Models\Accounting\BankGuarantee;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class ViewBankGuarantee extends ViewRecord
{
    protected static string $resource = BankGuaranteeResource::class;

    protected function getHeaderActions(): array
    {
        /** @var BankGuarantee $record */
        $record = $this->record;

        return [
            Actions\Action::make('renew')
                ->label(tr('actions.renew', [], null, 'dashboard'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => auth()->user()?->can('bank_guarantees.renew') ?? false)
                ->modalHeading(tr('actions.renew', [], null, 'dashboard'))
                ->modalDescription(tr('actions.renew_description', [], null, 'dashboard'))
                ->form([
                    Forms\Components\Placeholder::make('current_end_date')
                        ->label(tr('forms.bank_guarantees.fields.current_end_date', [], null, 'dashboard'))
                        ->content(fn (BankGuarantee $record) => $record->end_date->format('Y-m-d')),

                    Forms\Components\DatePicker::make('new_end_date')
                        ->label(tr('forms.bank_guarantees.fields.new_end_date', [], null, 'dashboard'))
                        ->required()
                        ->after(fn (BankGuarantee $record) => $record->end_date->format('Y-m-d'))
                        ->displayFormat('Y-m-d')
                        ->helperText(tr('forms.bank_guarantees.fields.new_end_date_helper', [], null, 'dashboard')),

                    Forms\Components\Textarea::make('notes')
                        ->label(tr('forms.bank_guarantees.fields.notes', [], null, 'dashboard'))
                        ->rows(3)
                        ->nullable(),
                ])
                ->action(function (array $data, BankGuarantee $record) {
                    try {
                        $newEndDate = new \DateTime($data['new_end_date']);

                        if ($newEndDate <= $record->end_date) {
                            throw ValidationException::withMessages([
                                'new_end_date' => tr('validation.renewal_date_must_be_after', [], null, 'dashboard'),
                            ]);
                        }

                        $oldEndDate = $record->end_date;
                        $record->renew($newEndDate, $data['notes'] ?? null);

                        Notification::make()
                            ->success()
                            ->title(tr('messages.renewed_successfully', [], null, 'dashboard'))
                            ->body(tr('messages.bank_guarantee_renewed', [
                                'old_date' => $oldEndDate->format('Y-m-d'),
                                'new_date' => $newEndDate->format('Y-m-d'),
                            ], null, 'dashboard'))
                            ->send();

                        $this->refreshFormData(['end_date', 'status']);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title(tr('messages.renewal_failed', [], null, 'dashboard'))
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->can('bank_guarantees.update') ?? false),
        ];
    }
}

