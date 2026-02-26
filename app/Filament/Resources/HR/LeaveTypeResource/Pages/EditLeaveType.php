<?php

namespace App\Filament\Resources\HR\LeaveTypeResource\Pages;

use App\Filament\Resources\HR\LeaveTypeResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;
use Illuminate\Support\Facades\Auth;

class EditLeaveType extends BaseEditRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => auth()->user()?->can('hr.leave_types.delete') ?? false),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

