<?php

namespace App\Filament\Resources\Accounting\VoucherSignatureResource\Pages;

use App\Filament\Resources\Accounting\VoucherSignatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditVoucherSignature extends EditRecord
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
            Actions\DeleteAction::make()
                ->label(trans_dash('common.delete', 'Delete'))
                ->before(function () {
                    if ($this->record->image_path) {
                        Storage::disk('public')->delete($this->record->image_path);
                    }
                }),
        ];
    }
}
