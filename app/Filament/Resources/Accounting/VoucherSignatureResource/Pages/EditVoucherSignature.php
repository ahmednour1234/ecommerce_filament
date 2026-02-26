<?php

namespace App\Filament\Resources\Accounting\VoucherSignatureResource\Pages;

use App\Filament\Resources\Accounting\VoucherSignatureResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;
use Illuminate\Support\Facades\Storage;

class EditVoucherSignature extends BaseEditRecord
{
    protected static string $resource = VoucherSignatureResource::class;

    public function getTitle(): string
    {
        return trans_dash('vouchers.signatures.edit_title', 'Edit Voucher Signature');
    }

    public function getBreadcrumb(): string
    {
        return trans_dash('common.edit', 'Edit');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->label(trans_dash('common.delete', 'Delete'))
                ->before(function () {
                    if ($this->record->image_path) {
                        Storage::disk('public')->delete($this->record->image_path);
                    }
                }),
        ];
    }
}
