<?php

namespace App\Filament\Resources\Housing\AccommodationEntryResource\Pages;

use App\Filament\Resources\Housing\AccommodationEntryResource;
use App\Models\Housing\AccommodationEntryStatusLog;
use App\Models\Housing\AccommodationEntryTransfer;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAccommodationEntry extends CreateRecord
{
    protected static string $resource = AccommodationEntryResource::class;

    // Temporary storage between hooks
    private array $pendingStatusLogs = [];
    private ?int $pendingTransferClientId = null;
    private mixed $pendingTransferContractFile = null;

    /** Temporary uploaded files keyed by status_key, set via $wire.upload() from the status table component */
    public array $statusPdfs = [];

    public function getTitle(): string
    {
        return 'إضافة إدخال إيواء جديد';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract transfer-specific fields
        $this->pendingTransferClientId    = $data['transfer_client_id'] ?? null;
        $this->pendingTransferContractFile = $data['transfer_contract_file'] ?? null;
        unset($data['transfer_client_id'], $data['transfer_contract_file']);

        // Extract and store status log data
        $this->pendingStatusLogs = json_decode($data['all_status_dates'] ?? '{}', true) ?? [];
        $selectedStatusKeys = array_values(array_filter((array) ($data['status_keys'] ?? [])));

        $data['status_keys'] = $selectedStatusKeys;
        $data['status_key'] = count($selectedStatusKeys) > 0
            ? end($selectedStatusKeys)
            : null;

        if (!empty($selectedStatusKeys) && empty($data['status_date']) && !empty($this->pendingStatusLogs[$data['status_key']])) {
            $data['status_date'] = $this->pendingStatusLogs[$data['status_key']];
        }

        unset($data['all_status_dates'], $data['status_date']);

        // These are not stored on the entry (accessed via laborer relation)
        unset($data['nationality_id'], $data['worker_passport_number']);

        $data['type']       = 'recruitment';
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $entry = $this->record;

        $selectedStatusKeys = array_values(array_filter((array) ($entry->status_keys ?? [])));
        $selectedLookup = array_flip($selectedStatusKeys);

        // Create status log rows
        foreach ($this->pendingStatusLogs as $statusKey => $statusDate) {
            if ($statusKey && $statusDate && isset($selectedLookup[$statusKey])) {
                AccommodationEntryStatusLog::create([
                    'accommodation_entry_id' => $entry->id,
                    'old_status_id'          => null,
                    'new_status_id'          => null,
                    'status_key'             => $statusKey,
                    'status_date'            => $statusDate,
                    'created_by'             => auth()->id(),
                ]);
            }
        }

        // Save PDF attachments to their respective status logs
        foreach ($this->statusPdfs as $statusKey => $uploadedFile) {
            if ($uploadedFile instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                $path = $uploadedFile->storePublicly('accommodation-entries/status-pdfs', 'public');
                AccommodationEntryStatusLog::where('accommodation_entry_id', $entry->id)
                    ->where('status_key', $statusKey)
                    ->update(['attachment' => $path]);
            }
        }

        // Create transfer record if applicable
        if ($entry->entry_type === 'transfer' && ($this->pendingTransferClientId || $this->pendingTransferContractFile)) {
            $file = $this->pendingTransferContractFile;
            AccommodationEntryTransfer::create([
                'accommodation_entry_id' => $entry->id,
                'transfer_client_id'     => $this->pendingTransferClientId,
                'contract_file_path'     => is_array($file) ? (reset($file) ?: null) : $file,
                'contract_file_name'     => is_array($file) ? (array_key_first($file) ?: null) : $file,
                'created_by'             => auth()->id(),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
