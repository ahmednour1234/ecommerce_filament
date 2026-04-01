<?php

namespace App\Filament\Resources\Rental;

use App\Filament\Resources\Rental\RentalContractResource\Pages;
use App\Filament\Resources\Rental\RentalContractResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Rental\RentalContract;
use App\Models\MainCore\Branch;
use App\Models\Sales\Customer;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\MainCore\Currency;
use App\Models\Client;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;


class RentalContractResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RentalContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'قسم التأجير';
    protected static ?string $navigationLabel = 'عقود التأجير';
    protected static ?int $navigationSort = 1;

    public static function getUserSection(): ?string
    {
        $user = auth()->user();
        if (! $user) {
            return null;
        }
        if ($user->hasRole('super_admin') || $user->type === User::TYPE_COMPANY_OWNER || $user->type === User::TYPE_SUPER_ADMIN) {
            return null;
        }
        return match ($user->type) {
            User::TYPE_CUSTOMER_SERVICE => 'customer_service',
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => 'accounts',
            default => null,
        };
    }

    public static function isCustomerServiceTabDisabled(): bool
    {
        return static::getUserSection() === 'accounts';
    }

    public static function isAccountsTabDisabled(): bool
    {
        return static::getUserSection() === 'customer_service';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('rental_contract_tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('خدمة العملاء')
                            ->icon('heroicon-o-user-group')
                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label(tr('rental.fields.branch', [], null, 'dashboard') ?: 'Branch')
                            ->options(function () {
                                return Cache::remember('rental.branches_limited', 21600, function () {
                                    return Branch::active()
                                        ->whereIn('name', ['حفر الباطن', 'الرياض', 'عرعر'])
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        Forms\Components\Select::make('customer_id')
                            ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                            ->options(function () {
                                return Cache::remember('rental.customers', 21600, function () {
                                    return Customer::active()->get()->pluck('name', 'id')->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('general.clients.name_ar', [], null, 'dashboard') ?: 'الاسم (عربي)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('national_id')
                                    ->label(tr('general.clients.national_id', [], null, 'dashboard') ?: 'رقم الهوية')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Client::class, 'national_id'),
                                Forms\Components\TextInput::make('mobile')
                                    ->label(tr('general.clients.mobile', [], null, 'dashboard') ?: 'الجوال')
                                    ->required()
                                    ->tel()
                                    ->maxLength(50),
                                Forms\Components\Radio::make('marital_status')
                                    ->label(tr('general.clients.marital_status', [], null, 'dashboard') ?: 'الحالة الاجتماعية')
                                    ->required()
                                    ->options([
                                        'single'   => tr('general.clients.single', [], null, 'dashboard') ?: 'أعزب',
                                        'married'  => tr('general.clients.married', [], null, 'dashboard') ?: 'متزوج',
                                        'divorced' => tr('general.clients.divorced', [], null, 'dashboard') ?: 'مطلق',
                                        'widowed'  => tr('general.clients.widowed', [], null, 'dashboard') ?: 'أرمل',
                                    ]),
                                Forms\Components\Radio::make('classification')
                                    ->label(tr('general.clients.classification', [], null, 'dashboard') ?: 'التصنيف')
                                    ->required()
                                    ->options([
                                        'new'     => tr('general.clients.new', [], null, 'dashboard') ?: 'جديد',
                                        'vip'     => tr('general.clients.vip', [], null, 'dashboard') ?: 'VIP',
                                        'blocked' => tr('general.clients.blocked', [], null, 'dashboard') ?: 'محظور',
                                    ])
                                    ->default('new'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $client = Client::create($data);
                                Cache::forget('rental.customers');
                                return $client->id;
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('request_no')
                            ->label(tr('rental.fields.request_no', [], null, 'dashboard') ?: 'Request No')
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('worker_id')
                            ->label(tr('rental.fields.worker', [], null, 'dashboard') ?: 'Worker')
                            ->options(function () {
                                return Cache::remember('rental.workers', 21600, function () {
                                    return Laborer::where('is_available', true)
                                        ->get()
                                        ->mapWithKeys(function ($worker) {
                                            return [$worker->id => "{$worker->name_ar} ({$worker->passport_number})"];
                                        })
                                        ->toArray();
                                });
                            })
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'الاسم (عربي)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('passport_number')
                                    ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'رقم الجواز')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Laborer::class, 'passport_number'),
                                Forms\Components\Select::make('nationality_id')
                                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'الجنسية')
                                    ->options(function () {
                                        return Nationality::where('is_active', true)
                                            ->whereIn('name_ar', ['الفلبين', 'بنغلادش', 'سريلانكا', 'اثيوبيا', 'اوغندا', 'كينيا', 'بورندي'])
                                            ->get()
                                            ->mapWithKeys(fn ($n) => [$n->id => $n->name_ar]);
                                    })
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('gender')
                                    ->label(tr('recruitment.fields.gender', [], null, 'dashboard') ?: 'الجنس')
                                    ->options([
                                        'male'   => tr('recruitment_contract.gender.male', [], null, 'dashboard') ?: 'ذكر',
                                        'female' => tr('recruitment_contract.gender.female', [], null, 'dashboard') ?: 'أنثى',
                                    ])
                                    ->nullable(),
                                Forms\Components\Select::make('experience')
                                    ->label(tr('recruitment.fields.experience', [], null, 'dashboard') ?: 'الخبرة')
                                    ->options([
                                        'unspecified' => tr('recruitment_contract.experience.unspecified', [], null, 'dashboard') ?: 'غير محدد',
                                        'new'         => tr('recruitment_contract.experience.new', [], null, 'dashboard') ?: 'جديد',
                                        'ex_worker'   => tr('recruitment_contract.experience.ex_worker', [], null, 'dashboard') ?: 'سبق العمل EX',
                                    ])
                                    ->nullable(),
                                Forms\Components\TextInput::make('phone_1')
                                    ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'الجوال')
                                    ->tel()
                                    ->maxLength(50)
                                    ->nullable(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $sarCurrencyId = Currency::where('code', 'SAR')->first()?->id ?? Currency::first()?->id;
                                $laborer = Laborer::create([
                                    'name_ar'                    => $data['name_ar'],
                                    'name_en'                    => $data['name_ar'],
                                    'passport_number'            => $data['passport_number'],
                                    'nationality_id'             => $data['nationality_id'],
                                    'gender'                     => $data['gender'] ?? null,
                                    'experience_level'           => $data['experience'] ?? null,
                                    'phone_1'                    => $data['phone_1'] ?? null,
                                    'is_available'               => true,
                                    'monthly_salary_currency_id' => $sarCurrencyId,
                                ]);
                                Cache::forget('rental.workers');
                                return $laborer->id;
                            })
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('start_date')
                            ->label(tr('rental.fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                            ->required()
                            ->default(now())
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && $get('duration') && $get('duration_type')) {
                                    $endDate = static::calculateEndDate($state, $get('duration'), $get('duration_type'));
                                    $set('end_date', $endDate);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('duration_type')
                            ->label(tr('rental.fields.duration_type', [], null, 'dashboard') ?: 'Duration Type')
                            ->options([
                                'day' => tr('rental.duration_type.day', [], null, 'dashboard') ?: 'Day',
                                'month' => tr('rental.duration_type.month', [], null, 'dashboard') ?: 'Month',
                                'year' => tr('rental.duration_type.year', [], null, 'dashboard') ?: 'Year',
                            ])
                            ->required()
                            ->default('month')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($get('start_date') && $get('duration')) {
                                    $endDate = static::calculateEndDate($get('start_date'), $get('duration'), $state);
                                    $set('end_date', $endDate);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('duration')
                            ->label(tr('rental.fields.duration', [], null, 'dashboard') ?: 'Duration')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($get('start_date') && $get('duration_type')) {
                                    $endDate = static::calculateEndDate($get('start_date'), $state, $get('duration_type'));
                                    $set('end_date', $endDate);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('end_date')
                            ->label(tr('rental.fields.end_date', [], null, 'dashboard') ?: 'End Date')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('status')
                            ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options(function () {
                                $user = auth()->user();
                                $isOwner = $user?->hasRole('super_admin')
                                    || $user?->type === \App\Models\User::TYPE_COMPANY_OWNER
                                    || $user?->type === \App\Models\User::TYPE_SUPER_ADMIN;

                                if (! $isOwner) {
                                    return [
                                        'pending_approval' => tr('rental.status.pending_approval', [], null, 'dashboard') ?: 'ينتظر الموافقة',
                                    ];
                                }

                                return [
                                    'pending_approval' => tr('rental.status.pending_approval', [], null, 'dashboard') ?: 'ينتظر الموافقة',
                                    'active'           => tr('rental.status.active', [], null, 'dashboard') ?: 'نشط',
                                    'suspended'        => tr('rental.status.suspended', [], null, 'dashboard') ?: 'معلق',
                                    'completed'        => tr('rental.status.completed', [], null, 'dashboard') ?: 'مكتمل',
                                    'cancelled'        => tr('rental.status.cancelled', [], null, 'dashboard') ?: 'ملغي',
                                    'returned'         => tr('rental.status.returned', [], null, 'dashboard') ?: 'مسترجعة',
                                    'archived'         => tr('rental.status.archived', [], null, 'dashboard') ?: 'مؤرشفة',
                                    'rejected'         => tr('rental.status.rejected', [], null, 'dashboard') ?: 'مرفوض',
                                ];
                            })
                            ->default('pending_approval')
                            ->disabled(function ($record) {
                                $user = auth()->user();
                                $isOwner = $user?->hasRole('super_admin')
                                    || $user?->type === \App\Models\User::TYPE_COMPANY_OWNER
                                    || $user?->type === \App\Models\User::TYPE_SUPER_ADMIN;
                                // Non-owners cannot change status at all
                                return ! $isOwner;
                            })
                            ->dehydrated(function ($record) {
                                $user = auth()->user();
                                $isOwner = $user?->hasRole('super_admin')
                                    || $user?->type === \App\Models\User::TYPE_COMPANY_OWNER
                                    || $user?->type === \App\Models\User::TYPE_SUPER_ADMIN;
                                // Still save value even when disabled for new records
                                return true;
                            })
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(tr('rental.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                            ]), // end خدمة العملاء tab

                        Forms\Components\Tabs\Tab::make('قسم الحسابات')
                            ->icon('heroicon-o-calculator')
                            ->disabled(fn () => static::isAccountsTabDisabled())
                            ->schema([
                Forms\Components\Section::make(tr('rental.contracts.pricing', [], null, 'dashboard') ?: 'Pricing')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label(tr('rental.fields.amount', [], null, 'dashboard') ?: 'المبلغ')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->prefix('ر.س')
                            ->reactive()
                            ->afterStateHydrated(function (callable $set, $record) {
                                if (!$record) return;
                                $amount        = (float) ($record->amount ?? 0);
                                $taxPercent    = (float) ($record->tax_percent ?? 0);
                                $discountType  = $record->discount_type ?? 'none';
                                $discountValue = (float) ($record->discount_value ?? 0);
                                $paidTotal     = (float) ($record->paid_total ?? 0);
                                $discount = match ($discountType) {
                                    'percent' => $amount * $discountValue / 100,
                                    'fixed'   => $discountValue,
                                    default   => 0,
                                };
                                $subtotal = max(0, $amount - $discount);
                                $taxValue = $subtotal * $taxPercent / 100;
                                $total    = $subtotal + $taxValue;
                                $set('subtotal', round($subtotal, 2));
                                $set('tax_value', round($taxValue, 2));
                                $set('total', round($total, 2));
                                $set('remaining_total', round(max(0, $total - $paidTotal), 2));
                            })
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $amount        = (float) ($get('amount') ?? 0);
                                $taxPercent    = (float) ($get('tax_percent') ?? 0);
                                $discountType  = $get('discount_type') ?? 'none';
                                $discountValue = (float) ($get('discount_value') ?? 0);
                                $paidTotal     = (float) ($get('paid_total') ?? 0);
                                $discount = match ($discountType) {
                                    'percent' => $amount * $discountValue / 100,
                                    'fixed'   => $discountValue,
                                    default   => 0,
                                };
                                $subtotal  = max(0, $amount - $discount);
                                $taxValue  = $subtotal * $taxPercent / 100;
                                $total     = $subtotal + $taxValue;
                                $set('subtotal', round($subtotal, 2));
                                $set('tax_value', round($taxValue, 2));
                                $set('total', round($total, 2));
                                $set('remaining_total', round(max(0, $total - $paidTotal), 2));
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tax_percent')
                            ->label(tr('rental.fields.tax_percent', [], null, 'dashboard') ?: 'Tax %')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $amount        = (float) ($get('amount') ?? 0);
                                $taxPercent    = (float) ($get('tax_percent') ?? 0);
                                $discountType  = $get('discount_type') ?? 'none';
                                $discountValue = (float) ($get('discount_value') ?? 0);
                                $paidTotal     = (float) ($get('paid_total') ?? 0);
                                $discount = match ($discountType) {
                                    'percent' => $amount * $discountValue / 100,
                                    'fixed'   => $discountValue,
                                    default   => 0,
                                };
                                $subtotal  = max(0, $amount - $discount);
                                $taxValue  = $subtotal * $taxPercent / 100;
                                $total     = $subtotal + $taxValue;
                                $set('subtotal', round($subtotal, 2));
                                $set('tax_value', round($taxValue, 2));
                                $set('total', round($total, 2));
                                $set('remaining_total', round(max(0, $total - $paidTotal), 2));
                            })
                            ->columnSpan(1),

                        Forms\Components\Select::make('discount_type')
                            ->label(tr('rental.fields.discount_type', [], null, 'dashboard') ?: 'Discount Type')
                            ->options([
                                'none'    => tr('rental.discount_type.none', [], null, 'dashboard') ?: 'None',
                                'percent' => tr('rental.discount_type.percent', [], null, 'dashboard') ?: 'Percent',
                                'fixed'   => tr('rental.discount_type.fixed', [], null, 'dashboard') ?: 'Fixed',
                            ])
                            ->default('none')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $amount        = (float) ($get('amount') ?? 0);
                                $taxPercent    = (float) ($get('tax_percent') ?? 0);
                                $discountType  = $get('discount_type') ?? 'none';
                                $discountValue = (float) ($get('discount_value') ?? 0);
                                $paidTotal     = (float) ($get('paid_total') ?? 0);
                                $discount = match ($discountType) {
                                    'percent' => $amount * $discountValue / 100,
                                    'fixed'   => $discountValue,
                                    default   => 0,
                                };
                                $subtotal  = max(0, $amount - $discount);
                                $taxValue  = $subtotal * $taxPercent / 100;
                                $total     = $subtotal + $taxValue;
                                $set('subtotal', round($subtotal, 2));
                                $set('tax_value', round($taxValue, 2));
                                $set('total', round($total, 2));
                                $set('remaining_total', round(max(0, $total - $paidTotal), 2));
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('discount_value')
                            ->label(tr('rental.fields.discount_value', [], null, 'dashboard') ?: 'Discount Value')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $amount        = (float) ($get('amount') ?? 0);
                                $taxPercent    = (float) ($get('tax_percent') ?? 0);
                                $discountType  = $get('discount_type') ?? 'none';
                                $discountValue = (float) ($get('discount_value') ?? 0);
                                $paidTotal     = (float) ($get('paid_total') ?? 0);
                                $discount = match ($discountType) {
                                    'percent' => $amount * $discountValue / 100,
                                    'fixed'   => $discountValue,
                                    default   => 0,
                                };
                                $subtotal  = max(0, $amount - $discount);
                                $taxValue  = $subtotal * $taxPercent / 100;
                                $total     = $subtotal + $taxValue;
                                $set('subtotal', round($subtotal, 2));
                                $set('tax_value', round($taxValue, 2));
                                $set('total', round($total, 2));
                                $set('remaining_total', round(max(0, $total - $paidTotal), 2));
                            })
                            ->visible(fn (callable $get) => $get('discount_type') !== 'none')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('discount_reason')
                            ->label(tr('rental.fields.discount_reason', [], null, 'dashboard') ?: 'سبب الخصم')
                            ->rows(3)
                            ->visible(fn (callable $get) => $get('discount_type') !== 'none')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subtotal')
                            ->label(tr('rental.fields.subtotal', [], null, 'dashboard') ?: 'المجموع الفرعي')
                            ->numeric()
                            ->prefix('ر.س')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('tax_value')
                            ->label(tr('rental.fields.tax_value', [], null, 'dashboard') ?: 'قيمة الضريبة')
                            ->numeric()
                            ->prefix('ر.س')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('total')
                            ->label(tr('rental.fields.total', [], null, 'dashboard') ?: 'الإجمالي')
                            ->numeric()
                            ->prefix('ر.س')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('paid_total')
                            ->label(tr('rental.fields.paid_total', [], null, 'dashboard') ?: 'المبلغ المدفوع')
                            ->numeric()
                            ->default(0)
                            ->step(0.01)
                            ->minValue(0)
                            ->prefix('ر.س')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $total     = (float) ($get('total') ?? 0);
                                $paidTotal = (float) ($get('paid_total') ?? 0);
                                $set('remaining_total', round(max(0, $total - $paidTotal), 2));
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('remaining_total')
                            ->label(tr('rental.fields.remaining_total', [], null, 'dashboard') ?: 'المتبقي')
                            ->numeric()
                            ->prefix('ر.س')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                    ])
                    ->columns(2),
                            ]), // end قسم الحسابات tab
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('rental.fields.contract_no', [], null, 'dashboard') ?: 'Contract No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('worker.name_ar')
                    ->label(tr('rental.fields.worker', [], null, 'dashboard') ?: 'Worker')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(tr('rental.fields.start_date', [], null, 'dashboard') ?: 'Start Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(tr('rental.fields.end_date', [], null, 'dashboard') ?: 'End Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label(tr('rental.fields.duration', [], null, 'dashboard') ?: 'Duration')
                    ->formatStateUsing(fn ($record) => "{$record->duration} " . tr("rental.duration_type.{$record->duration_type}", [], null, 'dashboard'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total')
                    ->label(tr('rental.fields.total', [], null, 'dashboard') ?: 'Total')
                    ->money('SAR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->colors([
                        'success'  => 'active',
                        'warning'  => ['suspended', 'pending_approval'],
                        'info'     => 'completed',
                        'danger'   => ['cancelled', 'rejected'],
                        'secondary' => 'returned',
                        'gray'     => 'archived',
                    ])
                    ->formatStateUsing(fn ($state) => tr("rental.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label(tr('rental.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'gray' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => tr("rental.payment_status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label(tr('rental.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(function () {
                        return Branch::active()->get()->pluck('name', 'id')->toArray();
                    }),

                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('rental.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->options([
                        'pending_approval' => tr('rental.status.pending_approval', [], null, 'dashboard') ?: 'ينتظر الموافقة',
                        'active'    => tr('rental.status.active', [], null, 'dashboard') ?: 'Active',
                        'suspended' => tr('rental.status.suspended', [], null, 'dashboard') ?: 'Suspended',
                        'completed' => tr('rental.status.completed', [], null, 'dashboard') ?: 'Completed',
                        'cancelled' => tr('rental.status.cancelled', [], null, 'dashboard') ?: 'Cancelled',
                        'returned'  => tr('rental.status.returned', [], null, 'dashboard') ?: 'Returned',
                        'archived'  => tr('rental.status.archived', [], null, 'dashboard') ?: 'Archived',
                        'rejected'  => tr('rental.status.rejected', [], null, 'dashboard') ?: 'مرفوض',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(tr('rental.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                    ->options([
                        'paid' => tr('rental.payment_status.paid', [], null, 'dashboard') ?: 'Paid',
                        'unpaid' => tr('rental.payment_status.unpaid', [], null, 'dashboard') ?: 'Unpaid',
                        'partial' => tr('rental.payment_status.partial', [], null, 'dashboard') ?: 'Partial',
                        'refunded' => tr('rental.payment_status.refunded', [], null, 'dashboard') ?: 'Refunded',
                    ]),

                Tables\Filters\Filter::make('start_date')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Start From'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Start Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_from'], fn ($q, $date) => $q->whereDate('start_date', '>=', $date))
                            ->when($data['start_until'], fn ($q, $date) => $q->whereDate('start_date', '<=', $date));
                    }),

                Tables\Filters\SelectFilter::make('customer_id')
                    ->label(tr('rental.fields.customer', [], null, 'dashboard') ?: 'Customer')
                    ->relationship('customer', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label(tr('rental.actions.approve', [], null, 'dashboard') ?: 'موافقة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(tr('rental.actions.approve_heading', [], null, 'dashboard') ?: 'الموافقة على العقد')
                    ->modalDescription(tr('rental.actions.approve_confirm', [], null, 'dashboard') ?: 'هل أنت متأكد من الموافقة على هذا العقد وتفعيله؟')
                    ->visible(fn ($record) => $record->status === 'pending_approval' && (
                        auth()->user()?->hasRole('super_admin') ||
                        auth()->user()?->type === \App\Models\User::TYPE_COMPANY_OWNER ||
                        auth()->user()?->type === \App\Models\User::TYPE_SUPER_ADMIN
                    ))
                    ->action(function ($record) {
                        $record->update(['status' => 'active']);
                        \Filament\Notifications\Notification::make()
                            ->title(tr('rental.actions.approved_success', [], null, 'dashboard') ?: 'تم تفعيل العقد بنجاح')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label(tr('rental.actions.reject', [], null, 'dashboard') ?: 'رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(tr('rental.actions.reject_heading', [], null, 'dashboard') ?: 'رفض العقد')
                    ->modalDescription(tr('rental.actions.reject_confirm', [], null, 'dashboard') ?: 'هل أنت متأكد من رفض هذا العقد؟')
                    ->visible(fn ($record) => $record->status === 'pending_approval' && (
                        auth()->user()?->hasRole('super_admin') ||
                        auth()->user()?->type === \App\Models\User::TYPE_COMPANY_OWNER ||
                        auth()->user()?->type === \App\Models\User::TYPE_SUPER_ADMIN
                    ))
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);
                        \Filament\Notifications\Notification::make()
                            ->title(tr('rental.actions.rejected_success', [], null, 'dashboard') ?: 'تم رفض العقد')
                            ->danger()
                            ->send();
                    }),
                EditAction::make(),
                TableDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\StatusLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalContracts::route('/'),
            'create' => Pages\CreateRentalContract::route('/create'),
            'view' => Pages\ViewRentalContract::route('/{record}'),
            'edit' => Pages\EditRentalContract::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.create') ?? false;
    }

    public static function canView(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.view') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('rental.contracts.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    protected static function calculateEndDate($startDate, $duration, $durationType): string
    {
        if (!$startDate || !$duration || !$durationType) {
            return now()->toDateString();
        }

        $duration = (int) $duration;
        $date = Carbon::parse($startDate);
        return match($durationType) {
            'day' => $date->addDays($duration)->toDateString(),
            'month' => $date->addMonths($duration)->toDateString(),
            'year' => $date->addYears($duration)->toDateString(),
            default => $date->addMonths($duration)->toDateString(),
        };
    }
}
