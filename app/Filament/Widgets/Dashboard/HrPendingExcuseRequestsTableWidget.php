<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\HR\ExcuseRequestResource;
use App\Models\HR\ExcuseRequest;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class HrPendingExcuseRequestsTableWidget extends BaseWidget
{
    protected static ?string $heading = 'تنبيهات HR: طلبات استئذان تنتظر الموافقة';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin')
            || $user?->can('hr_excuse_requests.view_any')
            || $user?->can('hr_excuse_requests.approve')
            || $user?->type === User::TYPE_HR_MANAGER;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ExcuseRequest::query()->pending()->with('employee')->latest('date')->limit(20))
            ->columns([
                TextColumn::make('employee.full_name')->label('الموظف')->searchable(),
                TextColumn::make('date')->label('التاريخ')->date('Y-m-d'),
                TextColumn::make('hours')->label('الساعات')->suffix(' س'),
                TextColumn::make('reason')->label('السبب')->limit(30),
            ])
            ->emptyStateHeading('لا توجد طلبات استئذان تنتظر الموافقة')
            ->paginated([10, 20])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('view_all')
                    ->label('عرض الكل')
                    ->url(ExcuseRequestResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'pending']]])),
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('approve')
                    ->label('موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(fn (ExcuseRequest $record) => app(\App\Services\HR\ExcuseRequestService::class)->approve($record, auth()->user()))
                    ->visible(fn (ExcuseRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr_excuse_requests.approve') ?? false)),
                \Filament\Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(fn (ExcuseRequest $record) => app(\App\Services\HR\ExcuseRequestService::class)->reject($record, auth()->user()))
                    ->visible(fn (ExcuseRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr_excuse_requests.reject') ?? false)),
                \Filament\Tables\Actions\Action::make('edit')
                    ->label('تعديل')
                    ->url(fn (ExcuseRequest $record) => ExcuseRequestResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
