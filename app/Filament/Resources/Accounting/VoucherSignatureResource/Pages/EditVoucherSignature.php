<?php

namespace App\Filament\Resources\Accounting\VoucherSignatureResource\Pages;

use App\Filament\Resources\Accounting\VoucherSignatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditVoucherSignature extends EditRecord
{
    protected static string $resource = VoucherSignatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function () {
                    // Delete image when deleting signature
                    if ($this->record->image_path) {
                        Storage::disk('public')->delete($this->record->image_path);
                    }
                }),
        ];
    }
}

