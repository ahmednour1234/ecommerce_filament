<?php

namespace App\Filament\Resources\CompanyVisas\CompanyVisaContractResource\Pages;

use App\Filament\Resources\CompanyVisas\CompanyVisaContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContracts extends ListRecords
{
    protected static string $resource = CompanyVisaContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('company_visas.actions.create_new_contract', [], null, 'dashboard') ?: 'إنشاء عقد استقدام جديد'),
        ];
    }
}
