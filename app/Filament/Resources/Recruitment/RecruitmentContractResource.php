<?php

namespace App\Filament\Resources\Recruitment;

use App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;
use App\Filament\Resources\Recruitment\RecruitmentContractResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Forms\Components\FileUpload;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\MainCore\Branch;
use App\Models\MainCore\Currency;
use App\Models\Client;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Profession;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Agent;
use App\Models\MainCore\Country;
use App\Models\HR\Employee;
use App\Data\SaudiGovernorates;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use App\Filament\Actions\EditAction;
use App\Filament\Actions\TableDeleteAction;
use App\Models\User;
use App\Services\Recruitment\RecruitmentContractService;


class RecruitmentContractResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RecruitmentContract::class;

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
            User::TYPE_CUSTOMER_SERVICE => RecruitmentContract::SECTION_CUSTOMER_SERVICE,
            User::TYPE_ACCOUNTANT, User::TYPE_GENERAL_ACCOUNTANT => RecruitmentContract::SECTION_ACCOUNTS,
            User::TYPE_COORDINATOR => RecruitmentContract::SECTION_COORDINATION,
            default => null,
        };
    }

    public static function isCustomerServiceTabDisabled(): bool
    {
        $section = static::getUserSection();
        return $section === RecruitmentContract::SECTION_ACCOUNTS || $section === RecruitmentContract::SECTION_COORDINATION;
    }

    public static function isAccountsTabDisabled(): bool
    {
        return static::getUserSection() === RecruitmentContract::SECTION_COORDINATION;
    }

    public static function canEditCurrentSection(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->type === User::TYPE_COMPANY_OWNER);
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'عقود الاستقدام';
    protected static ?string $navigationLabel = 'عقود الاستقدام';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('recruitment_contract_tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('خدمة العملاء')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\Section::make('بيانات العقد')
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
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name_ar')
                                                    ->label(tr('general.clients.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('national_id')
                                                    ->label(tr('general.clients.national_id', [], null, 'dashboard') ?: 'National ID')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(Client::class, 'national_id'),
                                                Forms\Components\TextInput::make('mobile')
                                                    ->label(tr('general.clients.mobile', [], null, 'dashboard') ?: 'Mobile')
                                                    ->required()
                                                    ->tel()
                                                    ->maxLength(50),
                                                Forms\Components\Radio::make('marital_status')
                                                    ->label(tr('general.clients.marital_status', [], null, 'dashboard') ?: 'Marital Status')
                                                    ->required()
                                                    ->options([
                                                        'single' => tr('general.clients.single', [], null, 'dashboard') ?: 'Single',
                                                        'married' => tr('general.clients.married', [], null, 'dashboard') ?: 'Married',
                                                        'divorced' => tr('general.clients.divorced', [], null, 'dashboard') ?: 'Divorced',
                                                        'widowed' => tr('general.clients.widowed', [], null, 'dashboard') ?: 'Widowed',
                                                    ]),
                                                Forms\Components\Radio::make('classification')
                                                    ->label(tr('general.clients.classification', [], null, 'dashboard') ?: 'Classification')
                                                    ->required()
                                                    ->options([
                                                        'new' => tr('general.clients.new', [], null, 'dashboard') ?: 'New',
                                                        'vip' => tr('general.clients.vip', [], null, 'dashboard') ?: 'VIP',
                                                        'blocked' => tr('general.clients.blocked', [], null, 'dashboard') ?: 'Blocked',
                                                    ])
                                                    ->default('new'),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                $client = Client::create($data);
                                                Cache::forget('recruitment_contracts.clients');
                                                return $client->id;
                                            })
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('branch_id')
                                            ->label(tr('recruitment_contract.fields.branch', [], null, 'dashboard') ?: 'Branch')
                                            ->options(function () {
                                                return Cache::remember('recruitment_contracts.branches', 21600, function () {
                                                    return Branch::active()
                                                        ->whereIn('name', ['حفر الباطن', 'الرياض', 'عرعر'])
                                                        ->get()
                                                        ->pluck('name', 'id')
                                                        ->toArray();
                                                });
                                            })
                                            ->default(fn () => auth()->user()?->branch_id)
                                            ->required()
                                            ->searchable()
                                            ->reactive()
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled() || ! (auth()->user()?->can('recruitment_contracts.assign_employee_branch') ?? false))
                                            ->dehydrated(true)
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('marketer_id')
                                            ->label(tr('recruitment_contract.fields.marketer', [], null, 'dashboard') ?: 'اسم الموظف')
                                            ->options(function () {
                                                return Cache::remember('recruitment_contracts.employees', 21600, function () {
                                                    return Employee::active()
                                                        ->get()
                                                        ->mapWithKeys(function ($employee) {
                                                            return [$employee->id => $employee->full_name];
                                                        })
                                                        ->toArray();
                                                });
                                            })
                                            ->default(fn () => auth()->user()?->employee?->id)
                                            ->searchable()
                                            ->nullable()
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled() || ! (auth()->user()?->can('recruitment_contracts.assign_employee_branch') ?? false))
                                            ->dehydrated(true)
                                            ->columnSpan(1),
                                        Forms\Components\DatePicker::make('gregorian_request_date')
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->label(tr('recruitment_contract.fields.gregorian_request_date', [], null, 'dashboard') ?: 'Gregorian Request Date')
                                            ->required()
                                            ->default(now())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('worker_id')
                                            ->label(tr('recruitment_contract.fields.worker', [], null, 'dashboard') ?: 'العاملة')
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
                                            ->createOptionForm([
                                                Forms\Components\TextInput::make('name_ar')
                                                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('passport_number')
                                                    ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'Passport Number')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(Laborer::class, 'passport_number'),
                                                Forms\Components\Select::make('nationality_id')
                                                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                                                    ->options(function () {
                                                        return Nationality::where('is_active', true)
                                                            ->whereIn('name_ar', ['الفلبين', 'بنغلادش', 'سريلانكا', 'اثيوبيا', 'اوغندا', 'كينيا', 'بورندي'])
                                                            ->get()
                                                            ->mapWithKeys(function ($nationality) {
                                                                $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                                                return [$nationality->id => $label];
                                                            });
                                                    })
                                                    ->searchable()
                                                    ->required(),
                                                Forms\Components\Select::make('profession_id')
                                                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'Profession')
                                                    ->options(function () {
                                                        return Profession::where('is_active', true)
                                                            ->get()
                                                            ->mapWithKeys(function ($profession) {
                                                                $label = app()->getLocale() === 'ar' ? $profession->name_ar : $profession->name_en;
                                                                return [$profession->id => $label];
                                                            })
                                                            ->toArray();
                                                    })
                                                    ->searchable()
                                                    ->required(),
                                                Forms\Components\Select::make('gender')
                                                    ->label(tr('recruitment.fields.gender', [], null, 'dashboard') ?: 'Gender')
                                                    ->options([
                                                        'male' => tr('recruitment_contract.gender.male', [], null, 'dashboard') ?: 'Male',
                                                        'female' => tr('recruitment_contract.gender.female', [], null, 'dashboard') ?: 'Female',
                                                    ])
                                                    ->nullable(),
                                                Forms\Components\Select::make('experience')
                                                    ->label(tr('recruitment.fields.experience', [], null, 'dashboard') ?: 'Experience')
                                                    ->options([
                                                        'unspecified' => tr('recruitment_contract.experience.unspecified', [], null, 'dashboard') ?: 'غير محدد',
                                                        'new' => tr('recruitment_contract.experience.new', [], null, 'dashboard') ?: 'جديد',
                                                        'ex_worker' => tr('recruitment_contract.experience.ex_worker', [], null, 'dashboard') ?: 'سبق العمل EX',
                                                    ])
                                                    ->nullable(),
                                                Forms\Components\TextInput::make('phone_1')
                                                    ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'Phone 1')
                                                    ->tel()
                                                    ->maxLength(50)
                                                    ->nullable(),
                                            ])
                                            ->createOptionUsing(function (array $data): int {
                                                $sarCurrencyId = Currency::where('code', 'SAR')->first()?->id ?? Currency::active()->first()?->id;
                                                $laborer = Laborer::create([
                                                    'name_ar' => $data['name_ar'],
                                                    'passport_number' => $data['passport_number'],
                                                    'nationality_id' => $data['nationality_id'],
                                                    'profession_id' => $data['profession_id'],
                                                    'gender' => $data['gender'] ?? null,
                                                    'experience_level' => $data['experience'] ?? null,
                                                    'phone_1' => $data['phone_1'] ?? null,
                                                    'is_available' => true,
                                                    'monthly_salary_currency_id' => $sarCurrencyId,
                                                ]);
                                                Cache::forget('recruitment_contracts.workers');
                                                return $laborer->id;
                                            })
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->nullable()
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                Forms\Components\Section::make('بيانات التأشيرة')
                                    ->schema([
                                        FileUpload::document('visa_image', 'recruitment_contracts/visa')
                                            ->label(tr('recruitment_contract.fields.visa_image', [], null, 'dashboard') ?: 'صورة التأشيرة')
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('visa_type')
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->label(tr('recruitment_contract.fields.visa_type', [], null, 'dashboard') ?: 'Visa Type')
                                            ->options([
                                                'domestic_labor' => tr('recruitment_contract.visa_type.domestic_labor', [], null, 'dashboard') ?: 'تأشيرة عمالة منزلية',
                                                'comprehensive_qualification' => tr('recruitment_contract.visa_type.comprehensive_qualification', [], null, 'dashboard') ?: 'تأشيرة التأهيل الشامل',
                                            ])
                                            ->required()
                                            ->reactive()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('visa_no')
                                            ->label(tr('recruitment_contract.fields.visa_no', [], null, 'dashboard') ?: 'Visa No')
                                            ->required()
                                            ->maxLength(255)
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('arrival_country_id')
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->label(tr('recruitment_contract.fields.arrival_country', [], null, 'dashboard') ?: 'محطة الوصول')
                                            ->options([
                                                'الرياض' => tr('recruitment_contract.arrival_station.riyadh', [], null, 'dashboard') ?: 'الرياض',
                                                'الدمام' => tr('recruitment_contract.arrival_station.dammam', [], null, 'dashboard') ?: 'الدمام',
                                                'جده' => tr('recruitment_contract.arrival_station.jeddah', [], null, 'dashboard') ?: 'جده',
                                                'الطائف' => tr('recruitment_contract.arrival_station.taif', [], null, 'dashboard') ?: 'الطائف',
                                                'القصيم' => tr('recruitment_contract.arrival_station.qassim', [], null, 'dashboard') ?: 'القصيم',
                                                'جيزان' => tr('recruitment_contract.arrival_station.jizan', [], null, 'dashboard') ?: 'جيزان',
                                                'سكاكا' => tr('recruitment_contract.arrival_station.sakaka', [], null, 'dashboard') ?: 'سكاكا',
                                                'المدينة المنورة' => tr('recruitment_contract.arrival_station.madinah', [], null, 'dashboard') ?: 'المدينة المنورة',
                                            ])
                                            ->searchable()
                                            ->nullable()
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('departure_country_id')
                                            ->label(tr('recruitment_contract.fields.departure_country', [], null, 'dashboard') ?: 'محطة القدوم')
                                            ->options([
                                                'نيروبي' => 'نيروبي',
                                                'كمبالا' => 'كمبالا',
                                                'مانيلا' => 'مانيلا',
                                                'كولومبو' => 'كولومبو',
                                                'دكا' => 'دكا',
                                                'اديس ابابا' => 'اديس ابابا',
                                                'دار السلام' => 'دار السلام',
                                            ])
                                            ->searchable()
                                            ->nullable()
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('receiving_station_id')
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->label(tr('recruitment_contract.fields.receiving_station', [], null, 'dashboard') ?: 'محطة الاستلام')
                                            ->options(SaudiGovernorates::all())
                                            ->searchable()
                                            ->nullable()
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                                Forms\Components\Section::make(tr('recruitment_contract.sections.musaned_data', [], null, 'dashboard') ?: 'بيانات مساند')
                                    ->schema([
                                        Forms\Components\TextInput::make('musaned_contract_no')
                                            ->label(tr('recruitment_contract.fields.musaned_contract_no', [], null, 'dashboard') ?: 'Musaned Contract No')
                                            ->maxLength(255)
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('musaned_documentation_contract_no')
                                            ->label(tr('recruitment_contract.fields.musaned_documentation_contract_no', [], null, 'dashboard') ?: 'رقم التوثيق الالكتروني بمساند')
                                            ->maxLength(255)
                                            ->nullable()
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                        Forms\Components\DatePicker::make('musaned_contract_date')
                                            ->label(tr('recruitment_contract.fields.musaned_contract_date', [], null, 'dashboard') ?: 'Musaned Contract Date')
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                        FileUpload::document('musaned_contract_file', 'recruitment_contracts/musaned')
                                            ->label(tr('recruitment_contract.fields.musaned_contract_file', [], null, 'dashboard') ?: 'ملف عقد مساند')
                                            ->disabled(fn () => static::isCustomerServiceTabDisabled())
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('قسم الحسابات')
                            ->icon('heroicon-o-currency-dollar')
                            ->visible(fn () => static::getUserSection() === null || static::getUserSection() === RecruitmentContract::SECTION_ACCOUNTS || static::getUserSection() === RecruitmentContract::SECTION_COORDINATION)
                            ->schema([
                                Forms\Components\Section::make('قسم الحسابات')
                                    ->schema([
                                        Forms\Components\Select::make('current_section')
                                            ->label('العقد عند القسم')
                                            ->options(RecruitmentContract::currentSectionOptions())
                                            ->visible(fn () => static::canEditCurrentSection())
                                            ->columnSpan(1),
                                        Forms\Components\Select::make('payment_status')
                                            ->label(tr('recruitment_contract.fields.payment_status', [], null, 'dashboard') ?: 'حالة الدفع')
                                            ->disabled(fn () => static::isAccountsTabDisabled())
                                            ->dehydrated(true)
                                            ->options([
                                                'partial' => 'جزئي',
                                                'paid' => 'كلي',
                                            ])
                                            ->default('partial')
                                            ->required()
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('total_cost')
                                            ->label(tr('recruitment_contract.fields.total_cost', [], null, 'dashboard') ?: 'إجمالي التكلفة')
                                            ->numeric()
                                            ->minValue(0)
                                            ->prefix('ر.س')
                                            ->disabled(fn () => static::isAccountsTabDisabled())
                                            ->dehydrated(true)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make(tr('recruitment_contract.sections.coordination', [], null, 'dashboard') ?: 'قسم التنسيق')
                            ->icon('heroicon-o-map')
                            ->visible(fn () => static::getUserSection() === null || static::getUserSection() === RecruitmentContract::SECTION_COORDINATION)
                            ->schema([
                                Forms\Components\Section::make(tr('recruitment_contract.sections.coordination', [], null, 'dashboard') ?: 'قسم التنسيق')
                                    ->schema([
                                        Forms\Components\Select::make('current_section')
                                            ->label('العقد عند القسم')
                                            ->options(RecruitmentContract::currentSectionOptions())
                                            ->default(RecruitmentContract::SECTION_CUSTOMER_SERVICE)
                                            ->required(fn () => static::canEditCurrentSection())
                                            ->visible(fn () => static::canEditCurrentSection())
                                            ->columnSpan(1),
                        Forms\Components\View::make('filament.forms.components.status-table')
                            ->viewData(function ($record, $get) {
                                $statusLabels = [
                                    'new' => tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'جديد',
                                    'external_office_approval' => tr('recruitment_contract.status.external_office_approval', [], null, 'dashboard') ?: 'موافقة المكتب الخارجي',
                                    'contract_accepted_external_office' => tr('recruitment_contract.status.contract_accepted_external_office', [], null, 'dashboard') ?: 'قبول العقد من مكتب الخارجي',
                                    'waiting_approval' => tr('recruitment_contract.status.waiting_approval', [], null, 'dashboard') ?: 'انتظار الابروف',
                                    'contract_accepted_labor_ministry' => tr('recruitment_contract.status.contract_accepted_labor_ministry', [], null, 'dashboard') ?: 'قبول العقد من مكتب العمل الخارجي',
                                    'sent_to_saudi_embassy' => tr('recruitment_contract.status.sent_to_saudi_embassy', [], null, 'dashboard') ?: 'إرسال التأشيرة إلى السفارة السعودية',
                                    'visa_issued' => tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم التفييز',
                                    'travel_permit_after_visa_issued' => tr('recruitment_contract.status.travel_permit_after_visa_issued', [], null, 'dashboard') ?: 'تصريح سفر بعد تم التفييز',
                                    'waiting_flight_booking' => tr('recruitment_contract.status.waiting_flight_booking', [], null, 'dashboard') ?: 'انتظار حجز تذكرة الطيران',
                                    'arrival_scheduled' => tr('recruitment_contract.status.arrival_scheduled', [], null, 'dashboard') ?: 'معاد الوصول',
                                    'received' => tr('recruitment_contract.status.received', [], null, 'dashboard') ?: 'تم الاستلام',
                                    'return_during_warranty' => tr('recruitment_contract.status.return_during_warranty', [], null, 'dashboard') ?: 'رجيع خلال فترة الضمان',
                                    'runaway' => tr('recruitment_contract.status.runaway', [], null, 'dashboard') ?: 'هروب',
                                ];

                                $statusDurations = [
                                    'new' => null,
                                    'external_office_approval' => 5,
                                    'contract_accepted_external_office' => 5,
                                    'waiting_approval' => 5,
                                    'contract_accepted_labor_ministry' => 4,
                                    'sent_to_saudi_embassy' => 7,
                                    'visa_issued' => 10,
                                    'travel_permit_after_visa_issued' => 6,
                                    'waiting_flight_booking' => null,
                                    'arrival_scheduled' => null,
                                    'received' => null,
                                    'return_during_warranty' => null,
                                    'runaway' => null,
                                ];

                                $statusDates = [];
                                $currentStatus = $get('status') ?? ($record->status ?? 'new');

                                if ($record && $record->exists) {
                                    $statusLogs = $record->statusLogs()->orderBy('created_at', 'desc')->get();

                                    foreach ($statusLogs as $log) {
                                        if (!isset($statusDates[$log->new_status])) {
                                            $statusDate = $log->status_date ?: $log->created_at->format('Y-m-d');
                                            $statusDates[$log->new_status] = $statusDate;
                                        }
                                    }
                                }

                                return [
                                    'statuses' => $statusLabels,
                                    'statusDates' => $statusDates,
                                    'statusDurations' => $statusDurations,
                                    'currentStatus' => $currentStatus,
                                    'statusStatePath' => 'data.status',
                                    'statusDateStatePath' => 'data.status_date',
                                ];
                            })
                            ->columnSpan(2),

                        Forms\Components\Hidden::make('status')
                            ->default('new')
                            ->required(),

                        Forms\Components\Hidden::make('status_date')
                            ->default(now()->toDateString())
                            ->required(),

                        Forms\Components\Hidden::make('all_status_dates')
                            ->default('{}'),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Radio::make('client_text_message')
                            ->label(tr('recruitment_contract.fields.client_text_message', [], null, 'dashboard') ?: 'رسالة نصية للعميل')
                            ->options([
                                'yes' => 'نعم',
                                'no' => 'لا',
                            ])
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Radio::make('client_rating')
                            ->label(tr('recruitment_contract.fields.client_rating', [], null, 'dashboard') ?: 'تقييم العميل')
                            ->options([
                                'yes' => 'نعم',
                                'no' => 'لا',
                            ])
                            ->nullable()
                            ->columnSpan(1),

                        FileUpload::document('client_rating_proof_image', 'recruitment_contracts/client_rating')
                            ->label(tr('recruitment_contract.fields.client_rating_proof_image', [], null, 'dashboard') ?: 'صورة إثبات التقييم')
                            ->columnSpan(1),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                Tables\Columns\TextColumn::make('contract_no')
                    ->label(tr('recruitment_contract.fields.contract_no', [], null, 'dashboard') ?: 'Contract No')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.name_ar')
                    ->label(tr('recruitment_contract.fields.client', [], null, 'dashboard') ?: 'Client')
                    ->formatStateUsing(fn ($state, $record) => app()->getLocale() === 'ar' ? $record->client->name_ar : $record->client->name_en)
                    ->searchable(query: function ($query, string $search): \Illuminate\Database\Eloquent\Builder {
                        return $query->whereHas('client', function ($q) use ($search) {
                            $q->where('name_ar', 'like', "%{$search}%")
                              ->orWhere('name_en', 'like', "%{$search}%");
                        });
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('branch.name')
                    ->label(tr('recruitment_contract.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'Status')
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'primary',
                        'external_office_approval', 'contract_accepted_external_office', 'waiting_approval', 'contract_accepted_labor_ministry' => 'info',
                        'sent_to_saudi_embassy', 'visa_issued', 'travel_permit_after_visa_issued', 'waiting_flight_booking', 'arrival_scheduled', 'received' => 'success',
                        'return_during_warranty' => 'warning',
                        'runaway' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => tr("recruitment_contract.status.{$state}", [], null, 'dashboard') ?: $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('current_section')
                    ->label('العقد عند القسم')
                    ->formatStateUsing(fn (?string $state, RecruitmentContract $record) => $record->status === 'received' ? 'تم التسليم' : ($state ? (RecruitmentContract::currentSectionOptions()[$state] ?? $state) : '—'))
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
                    ->label(tr('recruitment_contract.fields.total_cost', [], null, 'dashboard') ?: 'إجمالي التكلفة')
                    ->money('SAR')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('visa_no')
                    ->label(tr('recruitment_contract.fields.visa_no', [], null, 'dashboard') ?: 'Visa No')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('musaned_contract_no')
                    ->label(tr('recruitment_contract.fields.musaned_contract_no', [], null, 'dashboard') ?: 'Musaned Contract No')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

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
                        'new' => tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'جديد',
                        'external_office_approval' => tr('recruitment_contract.status.external_office_approval', [], null, 'dashboard') ?: 'موافقة المكتب الخارجي',
                        'contract_accepted_external_office' => tr('recruitment_contract.status.contract_accepted_external_office', [], null, 'dashboard') ?: 'قبول العقد من مكتب الخارجي',
                        'waiting_approval' => tr('recruitment_contract.status.waiting_approval', [], null, 'dashboard') ?: 'انتظار الابروف',
                        'contract_accepted_labor_ministry' => tr('recruitment_contract.status.contract_accepted_labor_ministry', [], null, 'dashboard') ?: 'قبول العقد من مكتب العمل الخارجي',
                        'sent_to_saudi_embassy' => tr('recruitment_contract.status.sent_to_saudi_embassy', [], null, 'dashboard') ?: 'إرسال التأشيرة إلى السفارة السعودية',
                        'visa_issued' => tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم التفييز',
                        'travel_permit_after_visa_issued' => tr('recruitment_contract.status.travel_permit_after_visa_issued', [], null, 'dashboard') ?: 'تصريح سفر بعد تم التفييز',
                        'waiting_flight_booking' => tr('recruitment_contract.status.waiting_flight_booking', [], null, 'dashboard') ?: 'انتظار حجز تذكرة الطيران',
                        'arrival_scheduled' => tr('recruitment_contract.status.arrival_scheduled', [], null, 'dashboard') ?: 'معاد الوصول',
                        'received' => tr('recruitment_contract.status.received', [], null, 'dashboard') ?: 'تم الاستلام',
                        'return_during_warranty' => tr('recruitment_contract.status.return_during_warranty', [], null, 'dashboard') ?: 'رجيع خلال فترة الضمان',
                        'runaway' => tr('recruitment_contract.status.runaway', [], null, 'dashboard') ?: 'هروب',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(tr('recruitment_contract.fields.payment_status', [], null, 'dashboard') ?: 'Payment Status')
                    ->options([
                        'paid' => tr('recruitment_contract.payment_status.paid', [], null, 'dashboard') ?: 'Paid',
                        'unpaid' => tr('recruitment_contract.payment_status.unpaid', [], null, 'dashboard') ?: 'Unpaid',
                        'partial' => tr('recruitment_contract.payment_status.partial', [], null, 'dashboard') ?: 'Partial',
                    ]),

                Tables\Filters\SelectFilter::make('current_section')
                    ->label('العقد عند القسم')
                    ->options(RecruitmentContract::currentSectionOptions()),

                Tables\Filters\SelectFilter::make('nationality_id')
                    ->label(tr('recruitment_contract.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                    ->options(function () {
                        return Cache::remember('recruitment_contracts.nationalities_filter', 21600, function () {
                            return Nationality::where('is_active', true)
                                ->whereIn('name_ar', ['الفلبين', 'بنغلادش', 'سريلانكا', 'اثيوبيا', 'اوغندا', 'كينيا', 'بورندي'])
                                ->get()
                                ->mapWithKeys(function ($nationality) {
                                    $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                    return [$nationality->id => $label];
                                })
                                ->toArray();
                        });
                    })
                    ->searchable(),

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
                Tables\Actions\Action::make('transfer_to_accounts')
                    ->label('توجيه لـ قسم الحسابات')
                    ->icon('heroicon-o-arrow-right')
                    ->color('success')
                    ->visible(fn (RecruitmentContract $record) => static::getUserSection() === RecruitmentContract::SECTION_CUSTOMER_SERVICE && $record->current_section === RecruitmentContract::SECTION_CUSTOMER_SERVICE)
                    ->action(function (RecruitmentContract $record) {
                        $record->update(['current_section' => RecruitmentContract::SECTION_ACCOUNTS]);
                        Notification::make()->title('تم التوجيه لـ قسم الحسابات')->success()->send();
                    }),
                Tables\Actions\Action::make('transfer_to_coordination')
                    ->label('توجيه لـ قسم التنسيق')
                    ->icon('heroicon-o-arrow-right')
                    ->color('success')
                    ->visible(fn (RecruitmentContract $record) => static::getUserSection() === RecruitmentContract::SECTION_ACCOUNTS && $record->current_section === RecruitmentContract::SECTION_ACCOUNTS)
                    ->action(function (RecruitmentContract $record) {
                        $record->update(['current_section' => RecruitmentContract::SECTION_COORDINATION]);
                        Notification::make()->title('تم التوجيه لـ قسم التنسيق')->success()->send();
                    }),
                Tables\Actions\Action::make('worker_delivered')
                    ->label('تم تسليم العاملة')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('delivery_date')
                            ->label('تاريخ التسليم')
                            ->required()
                            ->default(now()),
                    ])
                    ->visible(fn (RecruitmentContract $record) => static::getUserSection() === RecruitmentContract::SECTION_COORDINATION && $record->current_section === RecruitmentContract::SECTION_COORDINATION && $record->status !== 'received')
                    ->action(function (RecruitmentContract $record, array $data) {
                        $deliveryDate = \Carbon\Carbon::parse($data['delivery_date'])->toDateString();
                        $service = app(RecruitmentContractService::class);
                        $service->logStatusChange($record, $record->status, 'received', 'تم تسليم العاملة', $deliveryDate);
                        $record->update(['status' => 'received']);
                        Notification::make()->title('تم تسجيل تسليم العاملة')->success()->send();
                    }),
                Tables\Actions\ViewAction::make(),
                EditAction::make(),
                TableDeleteAction::make(),
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
            RelationManagers\StatusLogsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $section = static::getUserSection();
        if ($section === RecruitmentContract::SECTION_CUSTOMER_SERVICE || $section === null) {
            return $query;
        }
        if ($section === RecruitmentContract::SECTION_ACCOUNTS) {
            $query->whereIn('current_section', [RecruitmentContract::SECTION_ACCOUNTS, RecruitmentContract::SECTION_COORDINATION]);
            return $query;
        }
        if ($section === RecruitmentContract::SECTION_COORDINATION) {
            $query->where(function ($q) {
                $q->where('current_section', RecruitmentContract::SECTION_COORDINATION)
                    ->orWhere('status', 'received');
            });
            return $query;
        }
        return $query;
    }

    public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        $url = parent::getUrl($name, $parameters, $isAbsolute, $panel, $tenant);
        return static::addPublicToUrl($url);
    }

    protected static function addPublicToUrl(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';

        if (str_contains($path, '/admin/') && !str_contains($path, '/public/admin/')) {
            if (str_starts_with($path, '/public/')) {
                $path = substr($path, 7);
            }
            $newPath = str_replace('/admin/', '/public/admin/', $path);

            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

            return $scheme . '://' . $host . $newPath . $query . $fragment;
        }

        return $url;
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
