<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateComplaint extends BaseCreateRecord
{
    protected static string $resource = ComplaintResource::class;

    protected ?string $complaintsMessage = null;
    protected ?string $coordinationMessage = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->complaintsMessage = $data['complaints_message'] ?? null;
        $this->coordinationMessage = $data['coordination_message'] ?? null;
        unset($data['complaints_message'], $data['coordination_message']);
        return $data;
    }

    protected function afterCreate(): void
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
    }
}
