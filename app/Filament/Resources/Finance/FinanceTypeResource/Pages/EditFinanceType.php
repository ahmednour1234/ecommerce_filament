<?php

namespace App\Filament\Resources\Finance\FinanceTypeResource\Pages;

use App\Filament\Resources\Finance\FinanceTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinanceType extends EditRecord
{
    protected static string $resource = FinanceTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['name']) && is_array($data['name'])) {
            $data['name'] = [
                'ar' => $data['name']['ar'] ?? '',
                'en' => $data['name']['en'] ?? null,
            ];
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = [
            'ar' => $data['name']['ar'] ?? '',
            'en' => $data['name']['en'] ?? null,
        ];
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
