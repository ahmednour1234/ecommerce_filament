<?php

namespace App\Filament\Resources\MainCore\SettingResource\Pages;

use App\Filament\Resources\MainCore\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // If key is app.languages and value is JSON, decode it
        if (isset($data['key']) && $data['key'] === 'app.languages' && isset($data['value'])) {
            if (is_string($data['value'])) {
                $decoded = json_decode($data['value'], true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $data['value'] = $decoded;
                }
            }
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If key is app.languages and value is array, encode it as JSON
        if (isset($data['key']) && $data['key'] === 'app.languages' && isset($data['value'])) {
            if (is_array($data['value'])) {
                $data['value'] = json_encode($data['value']);
            }
        }
        return $data;
    }

    protected function afterSave(): void
    {
        // Set logo file visibility to public after save
        if ($this->record->logo) {
            try {
                \Illuminate\Support\Facades\Storage::disk('public')->setVisibility($this->record->logo, 'public');
            } catch (\Exception $e) {
                // Ignore if visibility cannot be set
            }
        }
    }
}
