<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use App\Filament\Pages\BaseCreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateBranchTransaction extends BaseCreateRecord
{
    protected static string $resource = BranchTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['branch_id'])) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            $fallbackBranchId = null;

            if ($user) {
                $fallbackBranchId = $user->branches()->pluck('branches.id')->first();
                $fallbackBranchId ??= $user->branch_id;
            }

            if (!empty($fallbackBranchId)) {
                $data['branch_id'] = (int) $fallbackBranchId;
            }
        }

        $data['created_by'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
