<?php

namespace App\Filament\Resources\HR\LoanResource\Pages;

use App\Filament\Resources\HR\LoanResource;
use App\Services\HR\LoanService;
use App\Filament\Pages\BaseCreateRecord;

class CreateLoan extends BaseCreateRecord
{
    protected static string $resource = LoanResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $service = app(LoanService::class);
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
