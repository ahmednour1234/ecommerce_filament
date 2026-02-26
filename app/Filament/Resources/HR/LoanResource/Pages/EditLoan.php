<?php

namespace App\Filament\Resources\HR\LoanResource\Pages;

use App\Filament\Resources\HR\LoanResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditLoan extends BaseEditRecord
{
    protected static string $resource = LoanResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            $service = app(\App\Services\HR\LoanService::class);
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
