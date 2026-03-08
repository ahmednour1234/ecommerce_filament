<?php

namespace App\Filament\Resources\HR/HrNotificationResource\Pages;

use App\Filament\Resources\HR\HrNotificationResource;
use App\Services\HR\HrNotificationService;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListHrNotifications extends ListRecords
{
    protected static string $resource = HrNotificationResource::class;

    public function getTabs(): array
    {
        $notificationService = app(HrNotificationService::class);
        $user = auth()->user();

        return [
            'all' => Tab::make(tr('tabs.all', [], null, 'dashboard') ?: 'All')
                ->badge(fn () => $notificationService->getNotifications([], $user)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query),

            'unread' => Tab::make(tr('tabs.unread', [], null, 'dashboard') ?: 'Unread')
                ->badge(fn () => $notificationService->getUnreadCount($user))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'unread')),

            'leave_requests' => Tab::make(tr('tabs.leave_requests', [], null, 'dashboard') ?: 'Leave Requests')
                ->badge(fn () => $notificationService->getNotifications(['type' => 'leave_request'], $user)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'leave_request')),

            'loans' => Tab::make(tr('tabs.loans', [], null, 'dashboard') ?: 'Loans')
                ->badge(fn () => $notificationService->getNotifications(['type' => 'loan'], $user)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'loan')),

            'excuse_requests' => Tab::make(tr('tabs.excuse_requests', [], null, 'dashboard') ?: 'Excuse Requests')
                ->badge(fn () => $notificationService->getNotifications(['type' => 'excuse_request'], $user)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'excuse_request')),

            'deductions' => Tab::make(tr('tabs.deductions', [], null, 'dashboard') ?: 'Deductions')
                ->badge(fn () => $notificationService->getNotifications(['type' => 'deduction'], $user)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'deduction')),

            'attendance_entries' => Tab::make(tr('tabs.attendance_entries', [], null, 'dashboard') ?: 'Attendance Entries')
                ->badge(fn () => $notificationService->getNotifications(['type' => 'attendance_entry'], $user)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'attendance_entry')),

            'payroll' => Tab::make(tr('tabs.payroll', [], null, 'dashboard') ?: 'Payroll')
                ->badge(fn () => $notificationService->getNotifications(['type' => 'payroll'], $user)->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'payroll')),
        ];
    }
}
