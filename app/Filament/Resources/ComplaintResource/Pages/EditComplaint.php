<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Services\ComplaintNotificationService;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditComplaint extends BaseEditRecord
{
    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $service = app(ComplaintNotificationService::class);
        $complaint = $this->record;
        
        if ($complaint->wasChanged('status') && $complaint->status === 'resolved') {
            $service->notifyOnResolved($complaint);
        }
        
        if ($complaint->wasChanged('branch_action_taken') && $complaint->branch_action_taken) {
            $service->notifyOnActionTaken($complaint);
        }
    }
}
