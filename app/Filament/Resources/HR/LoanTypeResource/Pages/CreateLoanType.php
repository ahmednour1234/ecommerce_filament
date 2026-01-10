<?php

namespace App\Filament\Resources\HR\LoanTypeResource\Pages;

use App\Filament\Resources\HR\LoanTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanType extends CreateRecord
{
    protected static string $resource = LoanTypeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['name_json'] = [
            'ar' => $data['name_ar'] ?? '',
            'en' => $data['name_en'] ?? '',
        ];
        $data['description_json'] = [
            'ar' => $data['description_ar'] ?? null,
            'en' => $data['description_en'] ?? null,
        ];
        unset($data['name_ar'], $data['name_en'], $data['description_ar'], $data['description_en']);
        return $data;
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $service = app(\App\Services\HR\LoanTypeService::class);
            return $service->create($data);
        } catch (\Exception $e) {
            $this->halt();
            $this->notify('danger', $e->getMessage());
            throw $e;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
