<?php

namespace App\Filament\Resources\Biometric;

use App\Filament\Resources\Biometric\BiometricAttendanceResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\AttendanceLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BiometricAttendanceResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = AttendanceLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'hr';
    protected static ?string $navigationLabel = 'سجلات الحضور الحيوية';
    protected static ?int $navigationSort = 16;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                            ->relationship('employee', 'employee_number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->employee_number . ' - ' . $record->full_name)
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('device_id')
                            ->label(tr('fields.device', [], null, 'dashboard') ?: 'Device')
                            ->relationship('device', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DateTimePicker::make('log_datetime')
                            ->label(tr('tables.biometric_attendances.attended_at', [], null, 'dashboard') ?: 'Log DateTime')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i'),

                        Forms\Components\Select::make('type')
                            ->label(tr('tables.biometric_attendances.type', [], null, 'dashboard') ?: 'Type')
                            ->options([
                                'check_in' => 'Check In',
                                'check_out' => 'Check Out',
                            ])
                            ->required(),

                        Forms\Components\Select::make('source')
                            ->label(tr('fields.source', [], null, 'dashboard') ?: 'Source')
                            ->options([
                                'manual' => 'Manual',
                                'device' => 'Device',
                                'api' => 'API',
                            ])
                            ->default('api')
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.employee_number')
                    ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                    ->formatStateUsing(fn ($record) => $record->employee->employee_number . ' - ' . $record->employee->full_name)
                    ->searchable(['employee.employee_number', 'employee.first_name', 'employee.last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('device.name')
                    ->label(tr('fields.device', [], null, 'dashboard') ?: 'Device')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('log_datetime')
                    ->label(tr('tables.biometric_attendances.attended_at', [], null, 'dashboard') ?: 'Log DateTime')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(tr('tables.biometric_attendances.type', [], null, 'dashboard') ?: 'Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'check_in' => 'success',
                        'check_out' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('source')
                    ->label(tr('fields.source', [], null, 'dashboard') ?: 'Source')
                    ->badge()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(tr('fields.employee', [], null, 'dashboard') ?: 'Employee')
                    ->relationship('employee', 'employee_number')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->employee_number . ' - ' . $record->full_name)
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('device_id')
                    ->label(tr('fields.device', [], null, 'dashboard') ?: 'Device')
                    ->relationship('device', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label(tr('tables.biometric_attendances.type', [], null, 'dashboard') ?: 'Type')
                    ->options([
                        'check_in' => 'Check In',
                        'check_out' => 'Check Out',
                    ]),

                Tables\Filters\SelectFilter::make('source')
                    ->label(tr('fields.source', [], null, 'dashboard') ?: 'Source')
                    ->options([
                        'manual' => 'Manual',
                        'device' => 'Device',
                        'api' => 'API',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('log_datetime', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBiometricAttendances::route('/'),
            'view' => Pages\ViewBiometricAttendance::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
