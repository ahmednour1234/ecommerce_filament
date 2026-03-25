<?php

namespace App\Filament\Resources\Housing\AccommodationEntryResource\Pages;

use App\Filament\Resources\Housing\AccommodationEntryResource;
use App\Models\Housing\AccommodationEntryStatusLog;
use App\Models\Housing\AccommodationEntryTransfer;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAccommodationEntry extends EditRecord
{
    protected static string $resource = AccommodationEntryResource::class;

    public function getTitle(): string
    {
        return 'تعديل إدخال الإيواء';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()->label('عرض'),
            Actions\DeleteAction::make()->label('حذف'),
        ];
    }

    // ── Fill form from existing record ────────────────────────────────────

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load status log dates into the all_status_dates JSON field
        $allDates = [];
        foreach ($this->record->statusLogs as $log) {
            if ($log->status_key && $log->status_date) {
                $allDates[$log->status_key] = is_string($log->status_date)
                    ? $log->status_date
                    : $log->status_date->toDateString();
            }
        }
        $data['all_status_dates'] = json_encode($allDates);

        // Load transfer data
        $transfer = $this->record->transferData;
        if ($transfer) {
            $data['transfer_client_id']    = $transfer->transfer_client_id;
            $data['transfer_contract_file'] = $transfer->contract_file_path;
        }

        return $data;
    }

    // ── Save with custom logic ────────────────────────────────────────────

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract transfer-specific fields
        $transferClientId     = $data['transfer_client_id'] ?? null;
        $transferContractFile = $data['transfer_contract_file'] ?? null;
        unset($data['transfer_client_id'], $data['transfer_contract_file']);

        // Extract status log data
        $allStatusDates = json_decode($data['all_status_dates'] ?? '{}', true) ?? [];
        unset($data['all_status_dates'], $data['status_date']);

        // These are not stored on the entry
        unset($data['nationality_id'], $data['worker_passport_number']);

        $data['updated_by'] = auth()->id();

        $record->update($data);

        // Upsert one log row per status key (preserves history, updates existing)
        foreach ($allStatusDates as $statusKey => $statusDate) {
            if ($statusKey && $statusDate) {
                AccommodationEntryStatusLog::updateOrCreate(
                    ['accommodation_entry_id' => $record->id, 'status_key' => $statusKey],
                    ['status_date' => $statusDate, 'created_by' => auth()->id()]
                );
            }
        }

        // Update or create transfer data
        if ($record->entry_type === 'transfer') {
            $file = $transferContractFile;
            AccommodationEntryTransfer::updateOrCreate(
                ['accommodation_entry_id' => $record->id],
                [
                    'transfer_client_id' => $transferClientId,
                    'contract_file_path' => is_array($file) ? (reset($file) ?: null) : $file,
                    'contract_file_name' => is_array($file) ? (array_key_first($file) ?: null) : $file,
                    'created_by'         => auth()->id(),
                ]
            );
        }

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
