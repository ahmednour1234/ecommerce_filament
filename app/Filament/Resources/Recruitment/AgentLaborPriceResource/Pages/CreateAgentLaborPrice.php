<?php

namespace App\Filament\Resources\Recruitment\AgentLaborPriceResource\Pages;

use App\Filament\Resources\Recruitment\AgentLaborPriceResource;
use App\Filament\Pages\BaseCreateRecord;
use Illuminate\Validation\Rules\Unique;

class CreateAgentLaborPrice extends BaseCreateRecord
{
    protected static string $resource = AgentLaborPriceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeValidate(array $data): array
    {
        return $data;
    }
}
