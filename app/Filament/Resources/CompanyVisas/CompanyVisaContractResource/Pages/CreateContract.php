<?php

namespace App\Filament\Resources\CompanyVisas\CompanyVisaContractResource\Pages;

use App\Filament\Resources\CompanyVisas\CompanyVisaContractResource;
use App\Filament\Pages\BaseCreateRecord;
use Modules\CompanyVisas\Entities\CompanyVisaRequest;

class CreateContract extends BaseCreateRecord
{
    protected static string $resource = CompanyVisaContractResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['visa_request_id']) && isset($data['workers_required'])) {
            $request = CompanyVisaRequest::find($data['visa_request_id']);
            if ($request && $data['workers_required'] > $request->remaining_count) {
                throw new \Filament\Support\Exceptions\Halt();
            }
        }

        return $data;
    }
}
