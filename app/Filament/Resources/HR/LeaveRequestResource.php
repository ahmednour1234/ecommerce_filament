<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\LeaveRequestResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\LeaveRequest;
use App\Models\HR\Employee;
use App\Models\HR\LeaveType;
use App\Filament\Forms\Components\FileUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeaveRequestResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'hr';
    protected static ?int $navigationSort = 41;
    protected static ?string $navigationTranslationKey = 'sidebar.hr.leaves_holidays.leave_requests';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                            ->relationship('employee', 'employee_number')
                            ->getOptionLabelFromRecordUsing(fn (Employee $record) => $record->employee_number . ' - ' . $record->full_name)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'pending')
                            ->columnSpan(1),

                        Forms\Components\Select::make('leave_type_id')
                            ->label(tr('fields.leave_type', [], null, 'dashboard') ?: 'Leave Type')
                            ->relationship('leaveType', 'name_en')
                            ->getOptionLabelFromRecordUsing(fn (LeaveType $record) => app()->getLocale() === 'ar' ? $record->name_ar : $record->name_en)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($record) => $record && $record->status !== 'pending')
                            ->native(false)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(tr('fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                            ->required()
                            ->native(false)
                            ->disabled(fn ($record) => $record && $record->status !== 'pending')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state && $get('end_date')) {
                                    $start = \Carbon\Carbon::parse($state);
                                    $end = \Carbon\Carbon::parse($get('end_date'));
                                    $days = $start->diffInDays($end) + 1;
                                    $set('total_days', $days);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(tr('fields.end_date', [], null, 'dashboard') ?: 'End Date')
                            ->required()
                            ->after('start_date')
                            ->native(false)
                            ->disabled(fn ($record) => $record && $record->status !== 'pending')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state && $get('start_date')) {
                                    $start = \Carbon\Carbon::parse($get('start_date'));
                                    $end = \Carbon\Carbon::parse($state);
                                    $days = $start->diffInDays($end) + 1;
                                    $set('total_days', $days);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total_days')
                            ->label(tr('fields.total_days', [], null, 'dashboard') ?: 'Total Days')
                            ->disabled()
                            ->dehydrated()
                            ->default(function ($get) {
                                if ($get('start_date') && $get('end_date')) {
                                    $start = \Carbon\Carbon::parse($get('start_date'));
                                    $end = \Carbon\Carbon::parse($get('end_date'));
                                    return $start->diffInDays($end) + 1;
                                }
                                return 0;
                            })
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('reason')
                            ->label(tr('fields.reason', [], null, 'dashboard') ?: 'Reason')
                            ->required()
                            ->rows(3)
                            ->maxLength(1000)
                            ->disabled(fn ($record) => $record && $record->status !== 'pending')
                            ->columnSpanFull(),

                        FileUpload::make('attachment_path')
                            ->label(tr('fields.attachment', [], null, 'dashboard') ?: 'Attachment')
                            ->directory('leave-requests')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(5120)
                            ->disabled(fn ($record) => $record && $record->status !== 'pending')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('manager_note')
                            ->label(tr('fields.manager_note', [], null, 'dashboard') ?: 'Manager Note')
                            ->rows(2)
                            ->maxLength(1000)
                            ->disabled(fn ($record) => !$record || $record->status === 'pending')
                            ->visible(fn ($record) => $record && in_array($record->status, ['approved', 'rejected']))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label(tr('tables.hr_leave_requests.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('tables.hr_leave_requests.employee_name', [], null, 'dashboard') ?: 'Employee Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('employee.department.name')
                    ->label(tr('tables.hr_leave_requests.department', [], null, 'dashboard') ?: 'Department')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label(tr('tables.hr_leave_requests.leave_type', [], null, 'dashboard') ?: 'Leave Type')
                    ->getStateUsing(fn (LeaveRequest $record) => app()->getLocale() === 'ar' ? $record->leaveType->name_ar : $record->leaveType->name_en)
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('tables.hr_leave_requests.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('tables.hr_leave_requests.end_date', [], null, 'dashboard') ?: 'End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_days')
                    ->label(tr('tables.hr_leave_requests.total_days', [], null, 'dashboard') ?: 'Total Days')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.hr_leave_requests.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => tr("status.{$state}", [], null, 'dashboard') ?: ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_leave_requests.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.hr_leave_requests.filters.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('status.pending', [], null, 'dashboard') ?: 'Pending',
                        'approved' => tr('status.approved', [], null, 'dashboard') ?: 'Approved',
                        'rejected' => tr('status.rejected', [], null, 'dashboard') ?: 'Rejected',
                        'cancelled' => tr('status.cancelled', [], null, 'dashboard') ?: 'Cancelled',
                    ])
                    ->native(false),

                Tables\Filters\SelectFilter::make('leave_type_id')
                    ->label(tr('tables.hr_leave_requests.filters.leave_type', [], null, 'dashboard') ?: 'Leave Type')
                    ->relationship('leaveType', 'name_en')
                    ->native(false),

                Tables\Filters\Filter::make('start_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('end_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(tr('actions.approve', [], null, 'dashboard') ?: 'Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('manager_note')
                            ->label(tr('fields.manager_note', [], null, 'dashboard') ?: 'Manager Note')
                            ->rows(3),
                    ])
                    ->action(function (LeaveRequest $record, array $data) {
                        app(\App\Services\HR\LeaveRequestService::class)->approve($record, $data['manager_note'] ?? null);
                    })
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr.leave_requests.approve') ?? false)),
                
                Tables\Actions\Action::make('reject')
                    ->label(tr('actions.reject', [], null, 'dashboard') ?: 'Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('manager_note')
                            ->label(tr('fields.manager_note', [], null, 'dashboard') ?: 'Manager Note')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (LeaveRequest $record, array $data) {
                        app(\App\Services\HR\LeaveRequestService::class)->reject($record, $data['manager_note'] ?? null);
                    })
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr.leave_requests.reject') ?? false)),
                
                Tables\Actions\Action::make('cancel')
                    ->label(tr('actions.cancel', [], null, 'dashboard') ?: 'Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (LeaveRequest $record) {
                        app(\App\Services\HR\LeaveRequestService::class)->cancel($record);
                    })
                    ->visible(fn (LeaveRequest $record) => in_array($record->status, ['pending', 'approved']) && (auth()->user()?->can('hr.leave_requests.cancel') ?? false)),
                
                Tables\Actions\EditAction::make()
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr.leave_requests.update') ?? false)),
                
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (LeaveRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr.leave_requests.delete') ?? false)),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'my-requests' => Pages\MyLeaveRequests::route('/my-requests'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr.leave_requests.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr.leave_requests.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr.leave_requests.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr.leave_requests.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny() || auth()->user()?->can('hr.leave_requests.view_own') ?? false;
    }
}

