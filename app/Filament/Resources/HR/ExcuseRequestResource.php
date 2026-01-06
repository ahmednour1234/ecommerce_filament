<?php

namespace App\Filament\Resources\HR;

use App\Filament\Resources\HR\ExcuseRequestResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\ExcuseRequest;
use App\Models\HR\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;

class ExcuseRequestResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = ExcuseRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 73;
    protected static ?string $navigationTranslationKey = 'navigation.hr_excuse_requests';

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
                            ->default(fn () => auth()->user()?->employee?->id ?? null)
                            ->disabled(fn () => !auth()->user()?->can('hr_excuse_requests.create') || auth()->user()?->employee?->id !== null),

                        Forms\Components\DatePicker::make('date')
                            ->label(tr('fields.date', [], null, 'dashboard') ?: 'Date')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('hours')
                            ->label(tr('fields.hours', [], null, 'dashboard') ?: 'Hours')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0.5)
                            ->maxValue(24)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state && $get('start_time')) {
                                    $startTime = Carbon::parse($get('start_time'));
                                    $endTime = $startTime->copy()->addHours($state);
                                    $set('end_time', $endTime->format('H:i'));
                                }
                            }),

                        Forms\Components\TimePicker::make('start_time')
                            ->label(tr('fields.start_time', [], null, 'dashboard') ?: 'Start Time')
                            ->required()
                            ->seconds(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state && $get('hours')) {
                                    $startTime = Carbon::parse($state);
                                    $endTime = $startTime->copy()->addHours($get('hours'));
                                    $set('end_time', $endTime->format('H:i'));
                                }
                            }),

                        Forms\Components\TimePicker::make('end_time')
                            ->label(tr('fields.end_time', [], null, 'dashboard') ?: 'End Time')
                            ->disabled()
                            ->seconds(false),

                        Forms\Components\Textarea::make('reason')
                            ->label(tr('fields.reason', [], null, 'dashboard') ?: 'Reason')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label(tr('fields.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('employee.full_name')
                    ->label(tr('fields.employee_name', [], null, 'dashboard') ?: 'Employee Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(tr('fields.date', [], null, 'dashboard') ?: 'Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('hours')
                    ->label(tr('fields.hours', [], null, 'dashboard') ?: 'Hours')
                    ->suffix(' h')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_time')
                    ->label(tr('fields.start_time', [], null, 'dashboard') ?: 'Start Time')
                    ->time('H:i')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label(tr('fields.end_time', [], null, 'dashboard') ?: 'End Time')
                    ->time('H:i')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label(tr('fields.approved_by', [], null, 'dashboard') ?: 'Approved By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label(tr('fields.approved_at', [], null, 'dashboard') ?: 'Approved At')
                    ->dateTime()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending' => tr('fields.pending', [], null, 'dashboard') ?: 'Pending',
                        'approved' => tr('fields.approved', [], null, 'dashboard') ?: 'Approved',
                        'rejected' => tr('fields.rejected', [], null, 'dashboard') ?: 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(tr('actions.approve', [], null, 'dashboard') ?: 'Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (ExcuseRequest $record) {
                        app(\App\Services\HR\ExcuseRequestService::class)->approve($record, auth()->user());
                    })
                    ->visible(fn (ExcuseRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr_excuse_requests.approve') ?? false)),
                Tables\Actions\Action::make('reject')
                    ->label(tr('actions.reject', [], null, 'dashboard') ?: 'Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->action(function (ExcuseRequest $record) {
                        app(\App\Services\HR\ExcuseRequestService::class)->reject($record, auth()->user());
                    })
                    ->visible(fn (ExcuseRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr_excuse_requests.reject') ?? false)),
                Tables\Actions\EditAction::make()
                    ->visible(fn (ExcuseRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr_excuse_requests.update') ?? false)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (ExcuseRequest $record) => $record->status === 'pending' && (auth()->user()?->can('hr_excuse_requests.delete') ?? false)),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExcuseRequests::route('/'),
            'create' => Pages\CreateExcuseRequest::route('/create'),
            'edit' => Pages\EditExcuseRequest::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_excuse_requests.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_excuse_requests.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_excuse_requests.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_excuse_requests.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

