<?php

namespace App\Filament\Resources\Packages\PackageResource\Pages;

use App\Filament\Resources\Packages\PackageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPackage extends EditRecord
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(tr('common.view', [], null, 'dashboard')),
            Actions\DeleteAction::make()
                ->label(tr('common.delete', [], null, 'dashboard')),
            Actions\RestoreAction::make()
                ->label(tr('common.restore', [], null, 'dashboard') ?: 'Restore'),
        ];
    }
}
