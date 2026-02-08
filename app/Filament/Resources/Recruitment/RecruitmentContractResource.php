<?php

namespace App\Filament\Resources\Recruitment;

use App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;
use App\Filament\Resources\Recruitment\RecruitmentContractResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Forms\Components\FileUpload;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\MainCore\Branch;
use App\Models\Client;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Profession;
use App\Models\MainCore\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class RecruitmentContractResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RecruitmentContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'recruitment_contracts';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_contracts.contracts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(tr('recruitment_contract.sections.basic_data', [], null, 'dashboard') ?: 'البيانات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('contract_no')
                            ->label(tr('recruitment_contract.fields.contract_no', [], null, 'dashboard') ?: 'Contract No')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('client_id')
                            ->label(tr('recruitment_contract.fields.client', [], null, 'dashboard') ?: 'Client')
                            ->options(function () {
                                return Cache::remember('recruitment_contracts.clients', 21600, function () {
                                    return Client::all()->mapWithKeys(function ($client) {
                                        $name = app()->getLocale() === 'ar' ? $client->name_ar : $client->name_en;
                                        return [$client->id => $name];
                                    })->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\Select::make('branch_id')
                            ->label(tr('recruitment_contract.fields.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(function () {
                                return Cache::remember('recruitment_contracts.branches', 21600, function () {
                                    return Branch::active()->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('gregorian_request_date')
                            ->label(tr('recruitment_contract.fields.gregorian_request_date', [], null, 'dashboard') ?: 'Gregorian Request Date')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('hijri_request_date')
                            ->label(tr('recruitment_contract.fields.hijri_request_date', [], null, 'dashboard') ?: 'Hijri Request Date')
                            ->columnSpan(1),

                        Forms\Components\Select::make('visa_type')
                            ->label(tr('recruitment_contract.fields.visa_type', [], null, 'dashboard') ?: 'Visa Type')
                            ->options([
                                'paid' => tr('recruitment_contract.visa_type.paid', [], null, 'dashboard') ?: 'مدفوع',
                                'qualification' => tr('recruitment_contract.visa_type.qualification', [], null, 'dashboard') ?: 'تأهيل',
                                'other' => tr('recruitment_contract.visa_type.other', [], null, 'dashboard') ?: 'أخرى',
                            ])
                            ->required()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('visa_no')
                            ->label(tr('recruitment_contract.fields.visa_no', [], null, 'dashboard') ?: 'Visa No')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('visa_date')
                            ->label(tr('recruitment_contract.fields.visa_date', [], null, 'dashboard') ?: 'Visa Date')
                            ->visible(fn (callable $get) => $get('visa_type') === 'paid')
                            ->required(fn (callable $get) => $get('visa_type') === 'paid')
                            ->columnSpan(1),

                        Forms\Components\Select::make('arrival_country_id')
                            ->label(tr('recruitment_contract.fields.arrival_country', [], null, 'dashboard') ?: 'محطة الوصول')
                            ->options(function () {
                                return Cache::remember('recruitment_contracts.countries', 21600, function () {
                                    return Country::where('is_active', true)
                                        ->get()
                                        ->pluck('name_text', 'id')
                                        ->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('departure_country_id')
                            ->label(tr('recruitment_contract.fields.departure_country', [], null, 'dashboard') ?: 'محطة القدوم')
                            ->options(function () {
                                return Cache::remember('recruitment_contracts.countries', 21600, function () {
                                    return Country::where('is_active', true)
                                        ->get()
                                        ->pluck('name_text', 'id')
                                        ->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('profession_id')
                            ->label(tr('recruitment_contract.fields.profession', [], null, 'dashboard') ?: 'Profession')
                            ->options(function () {
                                return Cache::remember('recruitment_contracts.professions', 21600, function () {
                                    return Profession::where('is_active', true)
                                        ->get()
                                        ->pluck('name_ar', 'id')
                                        ->toArray();
                                });
                            })
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('gender')
                            ->label(tr('recruitment_contract.fields.gender', [], null, 'dashboard') ?: 'Gender')
                            ->options([
                                'male' => tr('recruitment_contract.gender.male', [], null, 'dashboard') ?: 'Male',
                                'female' => tr('recruitment_contract.gender.female', [], null, 'dashboard') ?: 'Female',
                            ])
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('experience')
                            ->label(tr('recruitment_contract.fields.experience', [], null, 'dashboard') ?: 'Experience')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('religion')
                            ->label(tr('recruitment_contract.fields.religion', [], null, 'dashboard') ?: 'Religion')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment_contract.sections.additional_options', [], null, 'dashboard') ?: 'خيارات إضافية')
                    ->schema([
                        Forms\Components\TextInput::make('workplace_ar')
                            ->label(tr('recruitment_contract.fields.workplace_ar', [], null, 'dashboard') ?: 'Workplace (Arabic)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('workplace_en')
                            ->label(tr('recruitment_contract.fields.workplace_en', [], null, 'dashboard') ?: 'Workplace (English)')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('monthly_salary')
                            ->label(tr('recruitment_contract.fields.monthly_salary', [], null, 'dashboard') ?: 'Monthly Salary')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment_contract.sections.musaned_data', [], null, 'dashboard') ?: 'بيانات مساند')
                    ->schema([
                        Forms\Components\TextInput::make('musaned_contract_no')
                            ->label(tr('recruitment_contract.fields.musaned_contract_no', [], null, 'dashboard') ?: 'Musaned Contract No')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('musaned_auth_no')
                            ->label(tr('recruitment_contract.fields.musaned_auth_no', [], null, 'dashboard') ?: 'Musaned Auth No')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('musaned_contract_date')
                            ->label(tr('recruitment_contract.fields.musaned_contract_date', [], null, 'dashboard') ?: 'Musaned Contract Date')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment_contract.sections.financial_data', [], null, 'dashboard') ?: 'البيانات المالية')
                    ->schema([
                        Forms\Components\TextInput::make('direct_cost')
                            ->label(tr('recruitment_contract.fields.direct_cost', [], null, 'dashboard') ?: 'Direct Cost')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('internal_ticket_cost')
                            ->label(tr('recruitment_contract.fields.internal_ticket_cost', [], null, 'dashboard') ?: 'Internal Ticket Cost')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('external_cost')
                            ->label(tr('recruitment_contract.fields.external_cost', [], null, 'dashboard') ?: 'External Cost')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('vat_cost')
                            ->label(tr('recruitment_contract.fields.vat_cost', [], null, 'dashboard') ?: 'VAT Cost')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('gov_cost')
                            ->label(tr('recruitment_contract.fields.gov_cost', [], null, 'dashboard') ?: 'Government Cost')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('total_cost')
                            ->label(tr('recruitment_contract.fields.total_cost', [], null, 'dashboard') ?: 'Total Cost')
                            ->content(fn ($record) => $record ? number_format($record->total_cost, 2) : '0.00')
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('paid_total')
                            ->label(tr('recruitment_contract.fields.paid_total', [], null, 'dashboard') ?: 'Paid Total')
                            ->content(fn ($record) => $record ? number_format($record->paid_total, 2) : '0.00')
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('remaining_total')
                            ->label(tr('recruitment_contract.fields.remaining_total', [], null, 'dashboard') ?: 'Remaining Total')
                            ->content(fn ($record) => $record ? number_format($record->remaining_total, 2) : '0.00')
                            ->columnSpan(1),

                        Forms\Components\Select::make('payment_status')
                            ->label(tr('recruitment_contract.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                            ->options([
                                'unpaid' => tr('recruitment_contract.payment_status.unpaid', [], null, 'dashboard') ?: 'Unpaid',
                                'partial' => tr('recruitment_contract.payment_status.partial', [], null, 'dashboard') ?: 'Partial',
                                'paid' => tr('recruitment_contract.payment_status.paid', [], null, 'dashboard') ?: 'Paid',
                            ])
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment_contract.sections.other_data', [], null, 'dashboard') ?: 'البيانات الأخرى')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'Status')
                            ->options([
                                'new' => tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'New',
                                'processing' => tr('recruitment_contract.status.processing', [], null, 'dashboard') ?: 'Processing',
                                'contract_signed' => tr('recruitment_contract.status.contract_signed', [], null, 'dashboard') ?: 'Contract Signed',
                                'ticket_booked' => tr('recruitment_contract.status.ticket_booked', [], null, 'dashboard') ?: 'Ticket Booked',
                                'worker_received' => tr('recruitment_contract.status.worker_received', [], null, 'dashboard') ?: 'Worker Received',
                                'closed' => tr('recruitment_contract.status.closed', [], null, 'dashboard') ?: 'Closed',
                                'returned' => tr('recruitment_contract.status.returned', [], null, 'dashboard') ?: 'Returned',
                            ])
                            ->required()
                            ->default('new')
                            ->columnSpan(1),

                        Forms\Components\Select::make('worker_id')
                            ->label(tr('recruitment_contract.fields.worker', [], null, 'dashboard') ?: 'Worker')
                            ->options(function () {
                                return Cache::remember('recruitment_contracts.workers', 21600, function () {
                                    return Laborer::where('is_available', true)
                                        ->get()
                                        ->mapWithKeys(function ($worker) {
                                            return [$worker->id => "{$worker->name_ar} ({$worker->passport_number})"];
                                        })
                                        ->toArray();
                                });
                            })
                            ->searchable()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::document('visa_image', 'recruitment_contracts/visa')
                            ->label(tr('recruitment_contract.fields.visa_image', [], null, 'dashboard') ?: 'Visa Image')
                            ->columnSpan(1),

                        FileUpload::document('musaned_contract_file', 'recruitment_contracts/musaned')
                            ->label(tr('recruitment_contract.fields.musaned_contract_file', [], null, 'dashboard') ?: 'Musaned Contract File')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('recruitment_contract.fields.contract_no', [], null, 'dashboard') ?: 'Contract No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name_ar')
                    ->label(tr('recruitment_contract.fields.client', [], null, 'dashboard') ?: 'Client')
                    ->formatStateUsing(fn ($state, $record) => app()->getLocale() === 'ar' ? $record->client->name_ar : $record->client->name_en)
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('recruitment_contract.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->colors([
                        'primary' => 'new',
                        'warning' => 'processing',
                        'info' => 'contract_signed',
                        'success' => 'ticket_booked',
                        'success' => 'worker_received',
                        'gray' => 'closed',
                        'danger' => 'returned',
                    ])
                    ->formatStateUsing(fn ($state) => tr("recruitment_contract.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label(tr('recruitment_contract.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                    ])
                    ->formatStateUsing(fn ($state) => tr("recruitment_contract.payment_status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label(tr('recruitment_contract.fields.total_cost', [], null, 'dashboard') ?: 'Total Cost')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('common.created_at', [], null, 'dashboard') ?: 'Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('recruitment_contract.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(function () {
                        return Branch::active()->get()->pluck('name', 'id')->toArray();
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'new' => tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'New',
                        'processing' => tr('recruitment_contract.status.processing', [], null, 'dashboard') ?: 'Processing',
                        'contract_signed' => tr('recruitment_contract.status.contract_signed', [], null, 'dashboard') ?: 'Contract Signed',
                        'ticket_booked' => tr('recruitment_contract.status.ticket_booked', [], null, 'dashboard') ?: 'Ticket Booked',
                        'worker_received' => tr('recruitment_contract.status.worker_received', [], null, 'dashboard') ?: 'Worker Received',
                        'closed' => tr('recruitment_contract.status.closed', [], null, 'dashboard') ?: 'Closed',
                        'returned' => tr('recruitment_contract.status.returned', [], null, 'dashboard') ?: 'Returned',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(tr('recruitment_contract.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                    ->options([
                        'paid' => tr('recruitment_contract.payment_status.paid', [], null, 'dashboard') ?: 'Paid',
                        'unpaid' => tr('recruitment_contract.payment_status.unpaid', [], null, 'dashboard') ?: 'Unpaid',
                        'partial' => tr('recruitment_contract.payment_status.partial', [], null, 'dashboard') ?: 'Partial',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ReceiptsRelationManager::class,
            RelationManagers\ExpensesRelationManager::class,
            RelationManagers\StatusLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecruitmentContracts::route('/'),
            'create' => Pages\CreateRecruitmentContract::route('/create'),
            'view' => Pages\ViewRecruitmentContract::route('/{record}'),
            'edit' => Pages\EditRecruitmentContract::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment_contracts.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment_contracts.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment_contracts.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('recruitment_contracts.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('recruitment_contracts.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
