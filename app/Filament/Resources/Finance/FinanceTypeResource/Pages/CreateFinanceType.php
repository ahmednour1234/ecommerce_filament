<?php

namespace App\Filament\Resources\Finance\FinanceTypeResource\Pages;

use App\Filament\Resources\Finance\FinanceTypeResource;
use App\Filament\Pages\BaseCreateRecord;

class CreateFinanceType extends BaseCreateRecord
{
    protected static string $resource = FinanceTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
