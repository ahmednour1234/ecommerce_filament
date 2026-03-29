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
    protected mixed $complaintsAttachment = null;
    protected mixed $coordinationAttachment = null;

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
        $this->complaintsAttachment = $data['complaints_message_attachment'] ?? null;
        $this->coordinationAttachment = $data['coordination_message_attachment'] ?? null;
        unset($data['complaints_message'], $data['coordination_message'], $data['complaints_message_attachment'], $data['coordination_message_attachment']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->complaintsMessage) {
            $attachmentPath = null;
            if ($this->complaintsAttachment instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $attachmentPath = $this->complaintsAttachment->storePublicly('complaints/messages', 'public');
            } elseif (is_string($this->complaintsAttachment)) {
                $attachmentPath = $this->complaintsAttachment;
            }

            $this->record->messages()->create([
                'body'       => $this->complaintsMessage,
                'attachment' => $attachmentPath,
                'department' => 'complaints',
                'created_by' => auth()->id(),
            ]);
        }

        if ($this->coordinationMessage) {
            $attachmentPath = null;
            if ($this->coordinationAttachment instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $attachmentPath = $this->coordinationAttachment->storePublicly('complaints/messages', 'public');
            } elseif (is_string($this->coordinationAttachment)) {
                $attachmentPath = $this->coordinationAttachment;
            }

            $this->record->messages()->create([
                'body'       => $this->coordinationMessage,
                'attachment' => $attachmentPath,
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
