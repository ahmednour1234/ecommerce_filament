<?php

namespace App\Filament\Resources\Packages\PackageResource\Pages;

use App\Filament\Resources\Packages\PackageResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;

class EditPackage extends BaseEditRecord
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(tr('common.view', [], null, 'dashboard')),
            DeleteAction::make()
                ->label(tr('common.delete', [], null, 'dashboard')),
            Actions\RestoreAction::make()
                ->label(tr('common.restore', [], null, 'dashboard') ?: 'Restore'),
        ];
    }
}
