<?php

namespace App\Filament\Resources\HR\LeaveRequestResource\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Resources\HR\LeaveRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class MyLeaveRequests extends ListRecords
{
    use ExportsResourceTable;

    protected static string $resource = LeaveRequestResource::class;

    protected static ?string $title = 'My Leave Requests';

    public function getTitle(): string
    {
        return tr('pages.hr_leave_requests.my_requests', [], null, 'dashboard') ?: 'My Leave Requests';
    }

    protected function getTableQuery(): Builder
    {
        // Filter to show only current user's requests
        // This assumes there's a way to link user to employee
        // You may need to adjust this based on your user-employee relationship
        return parent::getTableQuery()
            ->where('created_by', auth()->id());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(tr('actions.add', [], null, 'dashboard') ?: 'Add'),
        ];
    }
}

