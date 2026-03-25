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

    protected ?string $complaintsMessage = null;
    protected ?string $coordinationMessage = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->complaintsMessage = $data['complaints_message'] ?? null;
        $this->coordinationMessage = $data['coordination_message'] ?? null;
        unset($data['complaints_message'], $data['coordination_message']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->complaintsMessage) {
            $this->record->messages()->create([
                'body'       => $this->complaintsMessage,
                'department' => 'complaints',
                'created_by' => auth()->id(),
            ]);
        }

        if ($this->coordinationMessage) {
            $this->record->messages()->create([
                'body'       => $this->coordinationMessage,
                'department' => 'coordination',
                'created_by' => auth()->id(),
            ]);
        }

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
