<?php

namespace App\Filament\Resources\HR\LoanTypeResource\Pages;

use App\Filament\Resources\HR\LoanTypeResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditLoanType extends BaseEditRecord
{
    protected static string $resource = LoanTypeResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $nameJson = $data['name_json'] ?? ['ar' => '', 'en' => ''];
        $descriptionJson = $data['description_json'] ?? ['ar' => null, 'en' => null];
        
        $data['name_ar'] = $nameJson['ar'] ?? '';
        $data['name_en'] = $nameJson['en'] ?? '';
        $data['description_ar'] = $descriptionJson['ar'] ?? null;
        $data['description_en'] = $descriptionJson['en'] ?? null;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $service = app(\App\Services\HR\LoanTypeService::class);
            return $service->update($record, $data);
        } catch (\Exception $e) {
            $this->halt();
            $this->notify('danger', $e->getMessage());
            throw $e;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
