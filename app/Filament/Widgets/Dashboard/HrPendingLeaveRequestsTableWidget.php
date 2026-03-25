<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\HR\LeaveRequestResource;
use App\Models\HR\LeaveRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class HrPendingLeaveRequestsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'تنبيهات HR: طلبات إجازة تنتظر الموافقة';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin')
            || $user?->can('hr.leave_requests.view_any')
            || $user?->can('hr.leave_requests.approve')
            || $user?->type === User::TYPE_HR_MANAGER;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(LeaveRequest::query()->pending()->with(['employee', 'leaveType'])->latest()->limit(20))
            ->columns([
                TextColumn::make('employee.full_name')->label('الموظف')->searchable(),
                TextColumn::make('leaveType.name_ar')->label('نوع الإجازة')->getStateUsing(fn ($r) => $r->leaveType?->name_ar ?? $r->leaveType?->name_en),
                TextColumn::make('start_date')->label('من')->date('Y-m-d'),
                TextColumn::make('end_date')->label('إلى')->date('Y-m-d'),
                TextColumn::make('total_days')->label('الأيام'),
            ])
            ->emptyStateHeading('لا توجد طلبات إجازة تنتظر الموافقة')
            ->paginated([10, 20])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('view_all')
                    ->label('عرض الكل')
                    ->url(LeaveRequestResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'pending']]])),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('manager_note')->label('ملاحظة')->rows(2)->nullable(),
                    ])
                    ->action(fn (LeaveRequest $record, array $data) => app(\App\Services\HR\LeaveRequestService::class)->approve($record, $data['manager_note'] ?? null))
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr.leave_requests.approve') ?? false)),
                \Filament\Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('manager_note')->label('ملاحظة')->required()->rows(2),
                    ])
                    ->action(fn (LeaveRequest $record, array $data) => app(\App\Services\HR\LeaveRequestService::class)->reject($record, $data['manager_note'] ?? null))
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr.leave_requests.reject') ?? false)),
                \Filament\Tables\Actions\Action::make('edit')
                    ->label('تعديل')
                    ->url(fn (LeaveRequest $record) => LeaveRequestResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
