<?php

namespace App\Filament\Resources\Rental\RentalContractResource\Pages;

use App\Filament\Resources\Rental\RentalContractResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewRentalContract extends ViewRecord
{
    protected static string $resource = RentalContractResource::class;

    protected function getHeaderActions(): array
    {
        $isOwner = fn () => auth()->user()?->hasRole('super_admin')
            || auth()->user()?->type === User::TYPE_COMPANY_OWNER
            || auth()->user()?->type === User::TYPE_SUPER_ADMIN;

        return [
            Actions\EditAction::make(),
            Actions\Action::make('approve')
                ->label(tr('rental.actions.approve', [], null, 'dashboard') ?: 'موافقة')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(tr('rental.actions.approve_heading', [], null, 'dashboard') ?: 'الموافقة على العقد')
                ->modalDescription(tr('rental.actions.approve_confirm', [], null, 'dashboard') ?: 'هل أنت متأكد من الموافقة على هذا العقد وتفعيله؟')
                ->visible(fn () => $this->record->status === 'pending_approval' && $isOwner())
                ->action(function () {
                    $this->record->update(['status' => 'active']);
                    Notification::make()
                        ->title(tr('rental.actions.approved_success', [], null, 'dashboard') ?: 'تم تفعيل العقد بنجاح')
                        ->success()
                        ->send();
                    $this->refreshFormData(['status']);
                }),
            Actions\Action::make('reject')
                ->label(tr('rental.actions.reject', [], null, 'dashboard') ?: 'رفض')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(tr('rental.actions.reject_heading', [], null, 'dashboard') ?: 'رفض العقد')
                ->modalDescription(tr('rental.actions.reject_confirm', [], null, 'dashboard') ?: 'هل أنت متأكد من رفض هذا العقد؟')
                ->visible(fn () => $this->record->status === 'pending_approval' && $isOwner())
                ->action(function () {
                    $this->record->update(['status' => 'rejected']);
                    Notification::make()
                        ->title(tr('rental.actions.rejected_success', [], null, 'dashboard') ?: 'تم رفض العقد')
                        ->danger()
                        ->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}

