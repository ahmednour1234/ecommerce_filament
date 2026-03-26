<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\HrNotificationResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\HrNotification;
use App\Services\HR\HrNotificationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HrNotificationResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = HrNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'الموارد البشرية';
    protected static ?string $navigationLabel = 'التنبيهات';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(tr('fields.title', [], null, 'dashboard') ?: 'Title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('message')
                            ->label(tr('fields.message', [], null, 'dashboard') ?: 'Message')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label(tr('fields.type', [], null, 'dashboard') ?: 'Type')
                            ->options([
                                'leave_request' => tr('types.leave_request', [], null, 'dashboard') ?: 'Leave Request',
                                'loan' => tr('types.loan', [], null, 'dashboard') ?: 'Loan',
                                'excuse_request' => tr('types.excuse_request', [], null, 'dashboard') ?: 'Excuse Request',
                                'deduction' => tr('types.deduction', [], null, 'dashboard') ?: 'Deduction',
                                'attendance_entry' => tr('types.attendance_entry', [], null, 'dashboard') ?: 'Attendance Entry',
                                'payroll' => tr('types.payroll', [], null, 'dashboard') ?: 'Payroll',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                            ->options([
                                'unread' => tr('status.unread', [], null, 'dashboard') ?: 'Unread',
                                'read' => tr('status.read', [], null, 'dashboard') ?: 'Read',
                                'action_taken' => tr('status.action_taken', [], null, 'dashboard') ?: 'Action Taken',
                            ])
                            ->required()
                            ->native(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if (!$user) {
                    return $query->whereRaw('1 = 0');
                }

                $notificationService = app(HrNotificationService::class);

                if ($user->can('hr_notifications.view_all') || $user->hasRole('super_admin')) {
                    return $query;
                }

                if ($user->can('hr_notifications.view_branch')) {
                    $branchIds = $user->branches()->pluck('branches.id')->toArray();
                    $branchId = $user->branch_id ?? ($user->branch ? $user->branch->id : null);
                    if (!empty($branchId)) {
                        $branchIds[] = (int) $branchId;
                    }
                    $branchIds = array_values(array_unique(array_filter($branchIds)));

                    if (!empty($branchIds)) {
                        return $query->whereIn('branch_id', $branchIds);
                    }

                    // No assigned branches means access to all branches.
                    return $query;
                }

                if ($user->employee) {
                    return $query->where('employee_id', $user->employee->id);
                }

                return $query->whereRaw('1 = 0');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(tr('tables.hr_notifications.title', [], null, 'dashboard') ?: 'Title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('message')
                    ->label(tr('tables.hr_notifications.message', [], null, 'dashboard') ?: 'Message')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),

                Tables\Columns\TextColumn::make('type')
                    ->label(tr('tables.hr_notifications.type', [], null, 'dashboard') ?: 'Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'leave_request' => 'info',
                        'loan' => 'warning',
                        'excuse_request' => 'success',
                        'deduction' => 'danger',
                        'attendance_entry' => 'primary',
                        'payroll' => 'secondary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'leave_request' => tr('types.leave_request', [], null, 'dashboard') ?: 'Leave Request',
                        'loan' => tr('types.loan', [], null, 'dashboard') ?: 'Loan',
                        'excuse_request' => tr('types.excuse_request', [], null, 'dashboard') ?: 'Excuse Request',
                        'deduction' => tr('types.deduction', [], null, 'dashboard') ?: 'Deduction',
                        'attendance_entry' => tr('types.attendance_entry', [], null, 'dashboard') ?: 'Attendance Entry',
                        'payroll' => tr('types.payroll', [], null, 'dashboard') ?: 'Payroll',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('tables.hr_notifications.employee', [], null, 'dashboard') ?: 'Employee')
                    ->searchable(['employee.first_name', 'employee.last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('tables.hr_notifications.branch', [], null, 'dashboard') ?: 'Branch')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.hr_notifications.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'success',
                        'action_taken' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unread' => tr('status.unread', [], null, 'dashboard') ?: 'Unread',
                        'read' => tr('status.read', [], null, 'dashboard') ?: 'Read',
                        'action_taken' => tr('status.action_taken', [], null, 'dashboard') ?: 'Action Taken',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_notifications.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('tables.hr_notifications.filters.type', [], null, 'dashboard') ?: 'Type')
                    ->options([
                        'leave_request' => tr('types.leave_request', [], null, 'dashboard') ?: 'Leave Request',
                        'loan' => tr('types.loan', [], null, 'dashboard') ?: 'Loan',
                        'excuse_request' => tr('types.excuse_request', [], null, 'dashboard') ?: 'Excuse Request',
                        'deduction' => tr('types.deduction', [], null, 'dashboard') ?: 'Deduction',
                        'attendance_entry' => tr('types.attendance_entry', [], null, 'dashboard') ?: 'Attendance Entry',
                        'payroll' => tr('types.payroll', [], null, 'dashboard') ?: 'Payroll',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.hr_notifications.filters.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'unread' => tr('status.unread', [], null, 'dashboard') ?: 'Unread',
                        'read' => tr('status.read', [], null, 'dashboard') ?: 'Read',
                        'action_taken' => tr('status.action_taken', [], null, 'dashboard') ?: 'Action Taken',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.hr_notifications.filters.branch', [], null, 'dashboard') ?: 'Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => Auth::user()?->can('hr_notifications.view_all') ?? false),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label(tr('fields.date_from', [], null, 'dashboard') ?: 'From Date'),
                        Forms\Components\DatePicker::make('date_to')
                            ->label(tr('fields.date_to', [], null, 'dashboard') ?: 'To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_read')
                    ->label(tr('actions.mark_as_read', [], null, 'dashboard') ?: 'Mark as Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (HrNotification $record) {
                        app(HrNotificationService::class)->markAsRead($record->id);
                    })
                    ->visible(fn (HrNotification $record) => $record->status === 'unread'),

                Tables\Actions\Action::make('view_related')
                    ->label(tr('actions.view_related', [], null, 'dashboard') ?: 'View Related')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (HrNotification $record) => $record->action_url)
                    ->openUrlInNewTab()
                    ->visible(fn (HrNotification $record) => !empty($record->action_url)),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('type')
                    ->label(tr('tables.hr_notifications.group_by_type', [], null, 'dashboard') ?: 'Group by Type'),
                Tables\Grouping\Group::make('status')
                    ->label(tr('tables.hr_notifications.group_by_status', [], null, 'dashboard') ?: 'Group by Status'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHrNotifications::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_notifications.view_any') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
