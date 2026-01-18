<?php

namespace App\Filament\Resources\HR;

use App\Filament\Forms\Components\FileUpload;
use App\Filament\Resources\HR\EmployeeResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\HrModuleGate;
use App\Models\HR\Employee;
use App\Models\HR\Department;
use App\Models\HR\Position;
use App\Models\MainCore\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    use TranslatableNavigation, HrModuleGate;

    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'HR';
    protected static ?int $navigationSort = 60;
    protected static ?string $navigationTranslationKey = 'navigation.hr_employees';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('EmployeeTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('basic_info')
                            ->label(tr('tabs.basic_info', [], null, 'dashboard') ?: 'Basic Info')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('employee_number')
                                            ->label(tr('fields.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('first_name')
                                            ->label(tr('fields.first_name', [], null, 'dashboard') ?: 'First Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('last_name')
                                            ->label(tr('fields.last_name', [], null, 'dashboard') ?: 'Last Name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('email')
                                            ->label(tr('fields.email', [], null, 'dashboard') ?: 'Email')
                                            ->email()
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('phone')
                                            ->label(tr('fields.phone', [], null, 'dashboard') ?: 'Phone')
                                            ->tel()
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('gender')
                                            ->label(tr('fields.gender', [], null, 'dashboard') ?: 'Gender')
                                            ->options([
                                                'male' => tr('gender.male', [], null, 'dashboard') ?: 'Male',
                                                'female' => tr('gender.female', [], null, 'dashboard') ?: 'Female',
                                            ])
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\DatePicker::make('birth_date')
                                            ->label(tr('fields.birth_date', [], null, 'dashboard') ?: 'Birth Date')
                                            ->displayFormat('d/m/Y')
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('fingerprint_device_id')
                                            ->label(tr('fields.fingerprint_device_id', [], null, 'dashboard') ?: 'Fingerprint Device ID')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        FileUpload::makeImage('profile_image', 'employees/profile')
                                            ->label(tr('fields.profile_image', [], null, 'dashboard') ?: 'Profile Image')
                                            ->imagePreviewHeight('200')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('job_info')
                            ->label(tr('tabs.job_info', [], null, 'dashboard') ?: 'Job Info')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\DatePicker::make('hire_date')
                                            ->label(tr('fields.hire_date', [], null, 'dashboard') ?: 'Hire Date')
                                            ->required()
                                            ->displayFormat('d/m/Y')
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('branch_id')
                                            ->label(tr('fields.branch', [], null, 'dashboard') ?: 'Branch')
                                            ->relationship('branch', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('department_id')
                                            ->label(tr('fields.department', [], null, 'dashboard') ?: 'Department')
                                            ->relationship('department', 'name', fn (Builder $query) => $query->active())
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->reactive()
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('position_id')
                                            ->label(tr('fields.position', [], null, 'dashboard') ?: 'Position')
                                            ->relationship(
                                                'position',
                                                'title',
                                                fn (Builder $query, callable $get) => $query
                                                    ->when(
                                                        $get('department_id'),
                                                        fn (Builder $query, $departmentId) => $query->where('department_id', $departmentId)
                                                    )
                                                    ->active()
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->reactive()
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('location_id')
                                            ->label(tr('fields.location', [], null, 'dashboard') ?: 'Location')
                                            ->options(function () {
                                                // Try to get locations if Location model exists
                                                if (class_exists(\App\Models\MainCore\Location::class)) {
                                                    return \App\Models\MainCore\Location::pluck('name', 'id');
                                                }
                                                // Fallback to branches if locations don't exist
                                                return Branch::pluck('name', 'id');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('basic_salary')
                                            ->label(tr('fields.basic_salary', [], null, 'dashboard') ?: 'Basic Salary')
                                            ->numeric()
                                            ->required()
                                            ->prefix('$')
                                            ->step(0.01)
                                            ->columnSpan(1),

                                        FileUpload::document('cv_file', 'employees/cv')
                                            ->label(tr('fields.cv_file', [], null, 'dashboard') ?: 'CV File')
                                            ->helperText(tr('fields.cv_file.helper', [], null, 'dashboard') ?: 'Upload CV/Resume file')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('personal_info')
                            ->label(tr('tabs.personal_info', [], null, 'dashboard') ?: 'Personal Info')
                            ->icon('heroicon-o-identification')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->label(tr('fields.address', [], null, 'dashboard') ?: 'Address')
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('city')
                                            ->label(tr('fields.city', [], null, 'dashboard') ?: 'City')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('country')
                                            ->label(tr('fields.country', [], null, 'dashboard') ?: 'Country')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('identity_type_id')
                                            ->label(tr('fields.identity_type', [], null, 'dashboard') ?: 'Identity Type')
                                            ->relationship('identityType', 'name', fn (Builder $query) => $query->active())
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('identity_number')
                                            ->label(tr('fields.identity_number', [], null, 'dashboard') ?: 'Identity Number')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\DatePicker::make('identity_expiry_date')
                                            ->label(tr('fields.identity_expiry_date', [], null, 'dashboard') ?: 'Identity Expiry Date')
                                            ->displayFormat('d/m/Y')
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('blood_type_id')
                                            ->label(tr('fields.blood_type', [], null, 'dashboard') ?: 'Blood Type')
                                            ->relationship('bloodType', 'name', fn (Builder $query) => $query->active())
                                            ->searchable()
                                            ->preload()
                                            ->nullable()
                                            ->native(false)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('emergency_contact_name')
                                            ->label(tr('fields.emergency_contact_name', [], null, 'dashboard') ?: 'Emergency Contact Name')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('emergency_contact_phone')
                                            ->label(tr('fields.emergency_contact_phone', [], null, 'dashboard') ?: 'Emergency Contact Phone')
                                            ->tel()
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('bank_info')
                            ->label(tr('tabs.bank_info', [], null, 'dashboard') ?: 'Bank Info')
                            ->icon('heroicon-o-building-library')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\Select::make('bank_id')
                                            ->label(tr('fields.bank', [], null, 'dashboard') ?: 'Bank')
                                            ->relationship('bank', 'name', fn (Builder $query) => $query->active())
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->nullable()
                                            ->native(false)
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('bank_name_text')
                                            ->label(tr('fields.bank_name_text', [], null, 'dashboard') ?: 'Bank Name (if not in list)')
                                            ->maxLength(255)
                                            ->visible(fn (callable $get) => !$get('bank_id'))
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('bank_account_number')
                                            ->label(tr('fields.bank_account_number', [], null, 'dashboard') ?: 'Bank Account Number')
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('iban')
                                            ->label(tr('fields.iban', [], null, 'dashboard') ?: 'IBAN')
                                            ->maxLength(255)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(tr('fields.status', [], null, 'dashboard') ?: 'Status')
                            ->options([
                                'active' => tr('fields.status_active', [], null, 'dashboard') ?: 'Active',
                                'inactive' => tr('fields.status_inactive', [], null, 'dashboard') ?: 'Inactive',
                            ])
                            ->default('active')
                            ->required()
                            ->native(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_number')
                    ->label(tr('tables.hr_employees.employee_number', [], null, 'dashboard') ?: 'Employee Number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label(tr('tables.hr_employees.name', [], null, 'dashboard') ?: 'Name')
                    ->formatStateUsing(fn (Employee $record) => $record->full_name)
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('tables.hr_employees.branch', [], null, 'dashboard') ?: 'Branch')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('department.name')
                    ->label(tr('tables.hr_employees.department', [], null, 'dashboard') ?: 'Department')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('position.title')
                    ->label(tr('tables.hr_employees.position', [], null, 'dashboard') ?: 'Position')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.hr_employees.status', [], null, 'dashboard') ?: 'Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => tr('fields.status_active', [], null, 'dashboard') ?: 'Active',
                        'inactive' => tr('fields.status_inactive', [], null, 'dashboard') ?: 'Inactive',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('hire_date')
                    ->label(tr('tables.hr_employees.hire_date', [], null, 'dashboard') ?: 'Hire Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.hr_employees.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.hr_employees.updated_at', [], null, 'dashboard') ?: 'Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.hr_employees.filters.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'active' => tr('fields.status_active', [], null, 'dashboard') ?: 'Active',
                        'inactive' => tr('fields.status_inactive', [], null, 'dashboard') ?: 'Inactive',
                    ]),

                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('tables.hr_employees.filters.branch', [], null, 'dashboard') ?: 'Branch')
                    ->relationship('branch', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('department_id')
                    ->label(tr('tables.hr_employees.filters.department', [], null, 'dashboard') ?: 'Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('position_id')
                    ->label(tr('tables.hr_employees.filters.position', [], null, 'dashboard') ?: 'Position')
                    ->relationship('position', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employees.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('hr_employees.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('hr_employees.delete') ?? false),
                ]),
            ])
            ->defaultSort('employee_number');
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('hr_employees.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('hr_employees.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('hr_employees.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('hr_employees.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('hr_employees.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

