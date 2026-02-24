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
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Agent;
use App\Models\MainCore\Country;
use App\Models\HR\Employee;
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
    protected static ?string $navigationLabel = 'عقود الاستقدام';
    protected static ?int $navigationSort = 1;

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
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('general.clients.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name_en')
                                    ->label(tr('general.clients.name_en', [], null, 'dashboard') ?: 'Name (English)')
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
                                Forms\Components\TextInput::make('email')
                                    ->label(tr('general.clients.email', [], null, 'dashboard') ?: 'Email')
                                    ->email()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('birth_date')
                                    ->label(tr('general.clients.birth_date', [], null, 'dashboard') ?: 'تاريخ الميلاد')
                                    ->required()
                                    ->placeholder('هـ / / ')
                                    ->helperText('أدخل التاريخ الهجري بصيغة: يوم/شهر/سنة (مثال: 15/03/1445)'),
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

                        Forms\Components\Select::make('marketer_id')
                            ->label(tr('recruitment_contract.fields.marketer', [], null, 'dashboard') ?: 'اسم المسوق')
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
                            ->searchable()
                            ->nullable()
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
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('visa_date')
                            ->label(tr('recruitment_contract.fields.visa_date', [], null, 'dashboard') ?: 'Visa Date')
                            ->visible(fn (callable $get) => $get('visa_type') === 'paid')
                            ->required(fn (callable $get) => $get('visa_type') === 'paid')
                            ->columnSpan(1),

                        Forms\Components\Select::make('arrival_country_id')
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
                            ->columnSpan(1),

                        Forms\Components\Select::make('receiving_station_id')
                            ->label(tr('recruitment_contract.fields.receiving_station', [], null, 'dashboard') ?: 'محطة الاستلام')
                            ->options([
                                'الرياض' => tr('recruitment_contract.receiving_station.riyadh', [], null, 'dashboard') ?: 'الرياض',
                                'جدة' => tr('recruitment_contract.receiving_station.jeddah', [], null, 'dashboard') ?: 'جدة',
                                'مكة المكرمة' => tr('recruitment_contract.receiving_station.makkah', [], null, 'dashboard') ?: 'مكة المكرمة',
                                'المدينة المنورة' => tr('recruitment_contract.receiving_station.madinah', [], null, 'dashboard') ?: 'المدينة المنورة',
                                'الدمام' => tr('recruitment_contract.receiving_station.dammam', [], null, 'dashboard') ?: 'الدمام',
                                'الخبر' => tr('recruitment_contract.receiving_station.khobar', [], null, 'dashboard') ?: 'الخبر',
                                'الطائف' => tr('recruitment_contract.receiving_station.taif', [], null, 'dashboard') ?: 'الطائف',
                                'بريدة' => tr('recruitment_contract.receiving_station.buraydah', [], null, 'dashboard') ?: 'بريدة',
                                'تبوك' => tr('recruitment_contract.receiving_station.tabuk', [], null, 'dashboard') ?: 'تبوك',
                                'حائل' => tr('recruitment_contract.receiving_station.hail', [], null, 'dashboard') ?: 'حائل',
                                'جازان' => tr('recruitment_contract.receiving_station.jazan', [], null, 'dashboard') ?: 'جازان',
                                'نجران' => tr('recruitment_contract.receiving_station.najran', [], null, 'dashboard') ?: 'نجران',
                                'أبها' => tr('recruitment_contract.receiving_station.abha', [], null, 'dashboard') ?: 'أبها',
                                'سكاكا' => tr('recruitment_contract.receiving_station.sakaka', [], null, 'dashboard') ?: 'سكاكا',
                                'الباحة' => tr('recruitment_contract.receiving_station.al_baha', [], null, 'dashboard') ?: 'الباحة',
                                'عرعر' => tr('recruitment_contract.receiving_station.arar', [], null, 'dashboard') ?: 'عرعر',
                                'القطيف' => tr('recruitment_contract.receiving_station.qatif', [], null, 'dashboard') ?: 'القطيف',
                                'الخفجي' => tr('recruitment_contract.receiving_station.khafji', [], null, 'dashboard') ?: 'الخفجي',
                                'ينبع' => tr('recruitment_contract.receiving_station.yanbu', [], null, 'dashboard') ?: 'ينبع',
                                'جيزان' => tr('recruitment_contract.receiving_station.jizan', [], null, 'dashboard') ?: 'جيزان',
                                'القريات' => tr('recruitment_contract.receiving_station.al_qurayyat', [], null, 'dashboard') ?: 'القريات',
                                'الجبيل' => tr('recruitment_contract.receiving_station.jubail', [], null, 'dashboard') ?: 'الجبيل',
                                'الرس' => tr('recruitment_contract.receiving_station.al_rass', [], null, 'dashboard') ?: 'الرس',
                                'عنيزة' => tr('recruitment_contract.receiving_station.onaizah', [], null, 'dashboard') ?: 'عنيزة',
                                'الزلفي' => tr('recruitment_contract.receiving_station.al_zulfi', [], null, 'dashboard') ?: 'الزلفي',
                                'الدوادمي' => tr('recruitment_contract.receiving_station.al_dawadmi', [], null, 'dashboard') ?: 'الدوادمي',
                                'المذنب' => tr('recruitment_contract.receiving_station.al_mithnab', [], null, 'dashboard') ?: 'المذنب',
                                'الشماسية' => tr('recruitment_contract.receiving_station.al_shamasiyah', [], null, 'dashboard') ?: 'الشماسية',
                                'البكيرية' => tr('recruitment_contract.receiving_station.al_bukayriyah', [], null, 'dashboard') ?: 'البكيرية',
                                'البدائع' => tr('recruitment_contract.receiving_station.al_badaei', [], null, 'dashboard') ?: 'البدائع',
                            ])
                            ->searchable()
                            ->nullable()
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

                        Forms\Components\Select::make('nationality_id')
                            ->label(tr('recruitment_contract.fields.nationality', [], null, 'dashboard') ?: 'Nationality')
                            ->options(function () {
                                return Cache::remember('recruitment_contracts.nationalities', 21600, function () {
                                    return Nationality::where('is_active', true)
                                        ->get()
                                        ->mapWithKeys(function ($nationality) {
                                            $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                            return [$nationality->id => $label];
                                        })
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

                        Forms\Components\Select::make('experience')
                            ->label(tr('recruitment_contract.fields.experience', [], null, 'dashboard') ?: 'Experience')
                            ->options([
                                'unspecified' => tr('recruitment_contract.experience.unspecified', [], null, 'dashboard') ?: 'غير محدد',
                                'new' => tr('recruitment_contract.experience.new', [], null, 'dashboard') ?: 'جديد',
                                'ex_worker' => tr('recruitment_contract.experience.ex_worker', [], null, 'dashboard') ?: 'سبق العمل EX',
                            ])
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('religion')
                            ->label(tr('recruitment_contract.fields.religion', [], null, 'dashboard') ?: 'Religion')
                            ->options([
                                'مسلم' => tr('recruitment_contract.religion.muslim', [], null, 'dashboard') ?: 'مسلم',
                                'غير مسلم' => tr('recruitment_contract.religion.non_muslim', [], null, 'dashboard') ?: 'غير مسلم',
                            ])
                            ->nullable()
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

                        Forms\Components\TextInput::make('musaned_documentation_contract_no')
                            ->label(tr('recruitment_contract.fields.musaned_documentation_contract_no', [], null, 'dashboard') ?: 'رقم التوثيق الالكتروني بمساند')
                            ->maxLength(255)
                            ->nullable()
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
                            ->default(function ($get, $record) {
                                if ($record) {
                                    return $record->payment_status;
                                }

                                $directCost = (float) ($get('direct_cost') ?? 0);
                                $internalTicketCost = (float) ($get('internal_ticket_cost') ?? 0);
                                $externalCost = (float) ($get('external_cost') ?? 0);
                                $vatCost = (float) ($get('vat_cost') ?? 0);
                                $govCost = (float) ($get('gov_cost') ?? 0);

                                $totalCost = $directCost + $internalTicketCost + $externalCost + $vatCost + $govCost;
                                $paidTotal = (float) ($get('paid_total') ?? 0);
                                $remainingTotal = max(0, $totalCost - $paidTotal);

                                if ($remainingTotal <= 0 && $totalCost > 0) {
                                    return 'paid';
                                } elseif ($paidTotal > 0) {
                                    return 'partial';
                                }
                                return 'unpaid';
                            })
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(tr('recruitment_contract.sections.other_data', [], null, 'dashboard') ?: 'البيانات الأخرى')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(tr('recruitment_contract.fields.status', [], null, 'dashboard') ?: 'Status')
                            ->options([
                                'new' => tr('recruitment_contract.status.new', [], null, 'dashboard') ?: 'جديد',
                                'foreign_embassy_approval' => tr('recruitment_contract.status.foreign_embassy_approval', [], null, 'dashboard') ?: 'موافقة السفارة الأجنبية',
                                'external_sending_office_approval' => tr('recruitment_contract.status.external_sending_office_approval', [], null, 'dashboard') ?: 'موافقة مكتب الإرسال الخارجيه',
                                'accepted_by_external_sending_office' => tr('recruitment_contract.status.accepted_by_external_sending_office', [], null, 'dashboard') ?: 'تم القبول من مكتب الإرسال الخارجيه',
                                'foreign_labor_ministry_approval' => tr('recruitment_contract.status.foreign_labor_ministry_approval', [], null, 'dashboard') ?: 'موافقة وزارة العمل الأجنبية',
                                'accepted_by_foreign_labor_ministry' => tr('recruitment_contract.status.accepted_by_foreign_labor_ministry', [], null, 'dashboard') ?: 'تم القبول من وزارة العمل الأجنبية',
                                'sent_to_saudi_embassy' => tr('recruitment_contract.status.sent_to_saudi_embassy', [], null, 'dashboard') ?: 'تم الإرسال للسفارة السعودية',
                                'visa_issued' => tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم إصدار التأشيرة',
                                'arrived_in_saudi_arabia' => tr('recruitment_contract.status.arrived_in_saudi_arabia', [], null, 'dashboard') ?: 'وصل للمملكة العربية السعودية',
                                'return_during_warranty' => tr('recruitment_contract.status.return_during_warranty', [], null, 'dashboard') ?: 'رجيع خلال فتره الضمان',
                                'outside_kingdom_during_warranty' => tr('recruitment_contract.status.outside_kingdom_during_warranty', [], null, 'dashboard') ?: 'خارج المملكه خلال فتره الضمان',
                                'labor_services_transfer' => tr('recruitment_contract.status.labor_services_transfer', [], null, 'dashboard') ?: 'نقل خدمات العماله المنزليه',
                                'runaway' => tr('recruitment_contract.status.runaway', [], null, 'dashboard') ?: 'هروب',
                                'temporary' => tr('recruitment_contract.status.temporary', [], null, 'dashboard') ?: 'مؤقته',
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
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'Name (Arabic)')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('name_en')
                                    ->label(tr('recruitment.fields.name_en', [], null, 'dashboard') ?: 'Name (English)')
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
                                            });
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
                                Forms\Components\TextInput::make('phone_1')
                                    ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'Phone 1')
                                    ->tel()
                                    ->maxLength(50)
                                    ->nullable(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $laborer = Laborer::create([
                                    'name_ar' => $data['name_ar'],
                                    'name_en' => $data['name_en'],
                                    'passport_number' => $data['passport_number'],
                                    'nationality_id' => $data['nationality_id'],
                                    'profession_id' => $data['profession_id'],
                                    'gender' => $data['gender'] ?? null,
                                    'phone_1' => $data['phone_1'] ?? null,
                                    'is_available' => true,
                                ]);
                                Cache::forget('recruitment_contracts.workers');
                                return $laborer->id;
                            })
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label(tr('recruitment_contract.fields.notes', [], null, 'dashboard') ?: 'Notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('client_text_message')
                            ->label(tr('recruitment_contract.fields.client_text_message', [], null, 'dashboard') ?: 'رسالة نصية للعميل')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('client_rating')
                            ->label(tr('recruitment_contract.fields.client_rating', [], null, 'dashboard') ?: 'تقييم العميل')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::document('client_rating_proof_image', 'recruitment_contracts/client_rating')
                            ->label(tr('recruitment_contract.fields.client_rating_proof_image', [], null, 'dashboard') ?: 'صورة إثبات التقييم')
                            ->columnSpan(1),

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
                        'foreign_embassy_approval', 'external_sending_office_approval', 'foreign_labor_ministry_approval' => 'info',
                        'accepted_by_external_sending_office', 'accepted_by_foreign_labor_ministry', 'visa_issued', 'arrived_in_saudi_arabia', 'labor_services_transfer' => 'success',
                        'sent_to_saudi_embassy', 'return_during_warranty', 'outside_kingdom_during_warranty', 'temporary' => 'warning',
                        'runaway' => 'danger',
                        default => 'gray',
                    })
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
                        'foreign_embassy_approval' => tr('recruitment_contract.status.foreign_embassy_approval', [], null, 'dashboard') ?: 'موافقة السفارة الأجنبية',
                        'external_sending_office_approval' => tr('recruitment_contract.status.external_sending_office_approval', [], null, 'dashboard') ?: 'موافقة مكتب الإرسال الخارجيه',
                        'accepted_by_external_sending_office' => tr('recruitment_contract.status.accepted_by_external_sending_office', [], null, 'dashboard') ?: 'تم القبول من مكتب الإرسال الخارجيه',
                        'foreign_labor_ministry_approval' => tr('recruitment_contract.status.foreign_labor_ministry_approval', [], null, 'dashboard') ?: 'موافقة وزارة العمل الأجنبية',
                        'accepted_by_foreign_labor_ministry' => tr('recruitment_contract.status.accepted_by_foreign_labor_ministry', [], null, 'dashboard') ?: 'تم القبول من وزارة العمل الأجنبية',
                        'sent_to_saudi_embassy' => tr('recruitment_contract.status.sent_to_saudi_embassy', [], null, 'dashboard') ?: 'تم الإرسال للسفارة السعودية',
                        'visa_issued' => tr('recruitment_contract.status.visa_issued', [], null, 'dashboard') ?: 'تم إصدار التأشيرة',
                        'arrived_in_saudi_arabia' => tr('recruitment_contract.status.arrived_in_saudi_arabia', [], null, 'dashboard') ?: 'وصل للمملكة العربية السعودية',
                        'return_during_warranty' => tr('recruitment_contract.status.return_during_warranty', [], null, 'dashboard') ?: 'رجيع خلال فتره الضمان',
                        'outside_kingdom_during_warranty' => tr('recruitment_contract.status.outside_kingdom_during_warranty', [], null, 'dashboard') ?: 'خارج المملكه خلال فتره الضمان',
                        'labor_services_transfer' => tr('recruitment_contract.status.labor_services_transfer', [], null, 'dashboard') ?: 'نقل خدمات العماله المنزليه',
                        'runaway' => tr('recruitment_contract.status.runaway', [], null, 'dashboard') ?: 'هروب',
                        'temporary' => tr('recruitment_contract.status.temporary', [], null, 'dashboard') ?: 'مؤقته',
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
