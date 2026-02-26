<?php

namespace App\Filament\Resources\HR\LeaveRequestResource\Pages;

use App\Filament\Resources\HR\LeaveRequestResource;
use Filament\Actions;
use App\Filament\Actions\DeleteAction;
use App\Filament\Pages\BaseEditRecord;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EditLeaveRequest extends BaseEditRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => $this->record->status === 'pending' && (auth()->user()?->can('hr.leave_requests.delete') ?? false)),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();
        
        // Recalculate total days if dates changed
        if (isset($data['start_date']) && isset($data['end_date'])) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $data['total_days'] = $startDate->diffInDays($endDate) + 1;
        }
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

