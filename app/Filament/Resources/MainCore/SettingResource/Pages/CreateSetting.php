<?php

namespace App\Filament\Resources\MainCore\SettingResource\Pages;

use App\Filament\Resources\MainCore\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // If key is app.languages and value is array, encode it as JSON
        if (isset($data['key']) && $data['key'] === 'app.languages' && isset($data['value'])) {
            if (is_array($data['value'])) {
                $data['value'] = json_encode($data['value']);
            }
        }
        return $data;
    }

    protected function afterCreate(): void
    {
        parent::afterCreate();
        
        // Set logo file visibility to public
        if ($this->record->logo) {
            \Illuminate\Support\Facades\Storage::disk('public')->setVisibility($this->record->logo, 'public');
        }
    }
}
