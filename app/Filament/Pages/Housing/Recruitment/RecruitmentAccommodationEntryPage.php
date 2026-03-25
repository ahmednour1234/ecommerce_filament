<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Client;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use App\Models\Recruitment\RecruitmentContract;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class RecruitmentAccommodationEntryPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?string $navigationLabel = 'ادخالات الايواء';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_housing.accommodation_entries';
    protected static string $view = 'filament.pages.housing.create-accommodation-entry';

    public array $data = [];

    // ── Modal state ───────────────────────────────────────────────
    public bool $showContractModal = false;
    public array $contractDetails = [];

    protected static function housingStatusOptions(): array
    {
        return [
            'unpaid_salary'         => 'عدم دفع راتب',
            'transfer_sponsorship'  => 'نقل كفاله',
            'temporary'             => 'مؤقته',
            'rental'                => 'ايجار',
            'work_refused'          => 'رفض عمل',
            'runaway'               => 'هروب',
            'ready_for_delivery'    => 'جاهز للتسليم',
            'with_client'           => 'مع العميل',
            'in_accommodation'      => 'في الايواء',
            'outside_kingdom'       => 'خارج المملكه',
            'ready_for_travel'      => 'جاهزه للتسفير',
        ];
    }

    // ── Section helpers ───────────────────────────────────────────

    public static function getUserSection(): ?string
    {
        $user = auth()->user();
        if (! $user) return null;
        if ($user->hasRole('super_admin') || in_array($user->type, [User::TYPE_COMPANY_OWNER, User::TYPE_SUPER_ADMIN])) {
            return null; // full access
        }
        return match ($user->type) {
            User::TYPE_COORDINATOR         => 'coordination',
            User::TYPE_COMPLAINTS_MANAGER  => 'complaints',
            default                        => null,
        };
    }

    public static function canAccessComplaintsTab(): bool
    {
        $section = static::getUserSection();
        return $section === null || $section === 'complaints';
    }

    public static function canAccessCoordinationTab(): bool
    {
        $section = static::getUserSection();
        return $section === null || $section === 'coordination';
    }

    // ── Shared schema builder ─────────────────────────────────────

    protected function buildSchema(bool $readonly = false): array
    {
        return [
            \Filament\Forms\Components\Section::make('بيانات العاملة والعميل')
                ->schema([
                    \Filament\Forms\Components\Select::make('contract_no')
                        ->label(tr('housing.accommodation.contract_no', [], null, 'dashboard') ?: 'رقم العقد')
                        ->options(function () {
                            return RecruitmentContract::query()
                                ->with('client')
                                ->get()
                                ->mapWithKeys(function ($contract) {
                                    $label = $contract->contract_no;
                                    if ($contract->client) {
                                        $label .= ' — ' . $contract->client->name_ar;
                                    }
                                    return [$contract->contract_no => $label];
                                })
                                ->toArray();
                        })
                        ->searchable()
                        ->live()
                        ->placeholder('اختر عقداً أو اتركه فارغاً للاختيار اليدوي')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->afterStateUpdated(function ($state, callable $set) use ($readonly) {
                            if ($readonly) return;
                            if ($state) {
                                $contract = RecruitmentContract::where('contract_no', $state)->first();
                                if ($contract) {
                                    if ($contract->worker_id) {
                                        $set('laborer_id', $contract->worker_id);
                                        $laborer = Laborer::find($contract->worker_id);
                                        if ($laborer) {
                                            $set('worker_passport_number', $laborer->passport_number);
                                            $set('nationality_id', $laborer->nationality_id);
                                        }
                                    }
                                    if ($contract->client_id) {
                                        $set('customer_id', $contract->client_id);
                                    }
                                }
                            } else {
                                $set('laborer_id', null);
                                $set('customer_id', null);
                                $set('worker_passport_number', null);
                                $set('nationality_id', null);
                            }
                        })
                        ->columnSpan(2),

                    \Filament\Forms\Components\Actions::make([
                        FormAction::make('viewContractDetails')
                            ->label('عرض بيانات العقد')
                            ->icon('heroicon-o-eye')
                            ->color('info')
                            ->size('sm')
                            ->visible(fn (\Filament\Forms\Get $get) => (bool) $get('contract_no'))
                            ->action(fn () => $this->viewContractDetails()),
                    ])->columnSpan(2),

                    \Filament\Forms\Components\Select::make('laborer_id')
                        ->label(tr('housing.accommodation.laborer', [], null, 'dashboard') ?: 'العاملة')
                        ->options(function (\Filament\Forms\Get $get) {
                            if ($get('contract_no')) {
                                return Laborer::orderBy('name_ar')
                                    ->get()
                                    ->mapWithKeys(fn ($w) => [$w->id => "{$w->name_ar} ({$w->passport_number})"])
                                    ->toArray();
                            }
                            return Cache::remember('recruitment_accommodation.workers', 21600, function () {
                                return Laborer::where('is_available', true)
                                    ->get()
                                    ->mapWithKeys(fn ($w) => [$w->id => "{$w->name_ar} ({$w->passport_number})"])
                                    ->toArray();
                            });
                        })
                        ->required(!$readonly)
                        ->searchable()
                        ->live()
                        ->disabled($readonly || fn (\Filament\Forms\Get $get) => (bool) $get('contract_no'))
                        ->dehydrated(true)
                        ->when(! $readonly, fn ($field) => $field
                            ->createOptionForm([
                                \Filament\Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'الاسم (عربي)')
                                    ->required()->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('passport_number')
                                    ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'رقم الجواز')
                                    ->required()->maxLength(255)->unique(Laborer::class, 'passport_number'),
                                \Filament\Forms\Components\Select::make('nationality_id')
                                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'الجنسية')
                                    ->options(function () {
                                        return Nationality::where('is_active', true)
                                            ->whereIn('name_ar', ['الفلبين', 'بنغلادش', 'سريلانكا', 'اثيوبيا', 'اوغندا', 'كينيا', 'بورندي'])
                                            ->get()
                                            ->mapWithKeys(fn ($n) => [$n->id => app()->getLocale() === 'ar' ? $n->name_ar : $n->name_en]);
                                    })->searchable()->required(),
                                \Filament\Forms\Components\Select::make('profession_id')
                                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'المهنة')
                                    ->options(function () {
                                        return Profession::where('is_active', true)
                                            ->get()
                                            ->mapWithKeys(fn ($p) => [$p->id => app()->getLocale() === 'ar' ? $p->name_ar : $p->name_en]);
                                    })->searchable()->required(),
                                \Filament\Forms\Components\Select::make('gender')
                                    ->label(tr('recruitment.fields.gender', [], null, 'dashboard') ?: 'الجنس')
                                    ->options(['male' => 'ذكر', 'female' => 'أنثى'])->nullable(),
                                \Filament\Forms\Components\Select::make('experience')
                                    ->label(tr('recruitment.fields.experience', [], null, 'dashboard') ?: 'الخبرة')
                                    ->options(['unspecified' => 'غير محدد', 'new' => 'جديد', 'ex_worker' => 'سبق العمل EX'])->nullable(),
                                \Filament\Forms\Components\TextInput::make('phone_1')
                                    ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'الجوال')
                                    ->tel()->maxLength(50)->nullable(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $laborer = Laborer::create([
                                    'name_ar'          => $data['name_ar'],
                                    'passport_number'  => $data['passport_number'],
                                    'nationality_id'   => $data['nationality_id'],
                                    'profession_id'    => $data['profession_id'],
                                    'gender'           => $data['gender'] ?? null,
                                    'experience_level' => $data['experience'] ?? null,
                                    'phone_1'          => $data['phone_1'] ?? null,
                                    'is_available'     => true,
                                ]);
                                Cache::forget('recruitment_accommodation.workers');
                                return $laborer->id;
                            })
                        )
                        ->afterStateUpdated(function ($state, callable $set) use ($readonly) {
                            if ($readonly) return;
                            if ($state) {
                                $laborer = Laborer::find($state);
                                if ($laborer) {
                                    $set('worker_passport_number', $laborer->passport_number);
                                    $set('nationality_id', $laborer->nationality_id);
                                }
                            }
                        })
                        ->columnSpan(1),

                    \Filament\Forms\Components\Select::make('customer_id')
                        ->label('العميل')
                        ->options(function () {
                            return Client::query()->get()->mapWithKeys(function ($client) {
                                $label = $client->name_ar;
                                if ($client->national_id) $label .= ' (' . $client->national_id . ')';
                                return [$client->id => $label];
                            })->toArray();
                        })
                        ->searchable()
                        ->disabled($readonly || fn (\Filament\Forms\Get $get) => (bool) $get('contract_no'))
                        ->dehydrated(true)
                        ->columnSpan(1),

                    \Filament\Forms\Components\Hidden::make('nationality_id'),
                    \Filament\Forms\Components\Hidden::make('worker_passport_number'),
                ])
                ->columns(2),

            \Filament\Forms\Components\Section::make('بيانات الإدخال')
                ->schema([
                    \Filament\Forms\Components\Select::make('entry_type')
                        ->label(tr('housing.accommodation.entry_type', [], null, 'dashboard') ?: 'نوع الدخول')
                        ->options([
                            'new_arrival' => tr('housing.accommodation.entry_type.new_arrival', [], null, 'dashboard') ?: 'وافد جديد',
                            'return'      => tr('housing.accommodation.entry_type.return', [], null, 'dashboard') ?: 'استرجاع',
                            'transfer'    => tr('housing.recruitment.entry_type.transfer', [], null, 'dashboard') ?: 'نقل كفالة',
                        ])
                        ->required(!$readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->columnSpan(1),

                    \Filament\Forms\Components\DateTimePicker::make('entry_date')
                        ->label(tr('housing.accommodation.entry_date', [], null, 'dashboard') ?: 'تاريخ الدخول')
                        ->required(!$readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->native(false)
                        ->columnSpan(1),

                    \Filament\Forms\Components\DateTimePicker::make('exit_date')
                        ->label(tr('housing.accommodation.exit_date', [], null, 'dashboard') ?: 'تاريخ خروج')
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->native(false)
                        ->columnSpan(1),

                    \Filament\Forms\Components\Select::make('building_id')
                        ->label(tr('housing.accommodation.building', [], null, 'dashboard') ?: 'المبنى')
                        ->options(function () {
                            return \App\Models\Housing\Building::availableRecruitment()
                                ->get()
                                ->mapWithKeys(fn ($b) => [
                                    $b->id => $b->name . ' (' . $b->available_capacity . ' ' . tr('common.available', [], null, 'dashboard') . ')'
                                ])
                                ->toArray();
                        })
                        ->required(!$readonly)
                        ->disabled($readonly)
                        ->dehydrated(true)
                        ->searchable()
                        ->helperText(tr('housing.accommodation.available_buildings_note', [], null, 'dashboard') ?: 'يتم عرض المباني المتاحة فقط (السعة المتاحة > 0)')
                        ->columnSpan(1),
                ])
                ->columns(2),

            \Filament\Forms\Components\Section::make('سجل الحالات')
                ->schema([
                    \Filament\Forms\Components\View::make('filament.forms.components.housing-status-table')
                        ->viewData(function ($get) use ($readonly) {
                            $statusLabels  = static::housingStatusOptions();
                            $currentStatus = $get('status_key') ?? '';
                            $allDates      = json_decode($get('all_status_dates') ?? '{}', true) ?? [];
                            $statusDates   = [];
                            foreach ($statusLabels as $key => $label) {
                                if (isset($allDates[$key])) $statusDates[$key] = $allDates[$key];
                            }
                            return [
                                'statuses'            => $statusLabels,
                                'statusDates'         => $statusDates,
                                'statusDurations'     => [],
                                'currentStatus'       => $currentStatus,
                                'statusStatePath'     => 'data.status_key',
                                'statusDateStatePath' => 'data.status_date',
                                'readonly'            => $readonly,
                            ];
                        })
                        ->columnSpanFull(),

                    \Filament\Forms\Components\Hidden::make('status_key'),
                    \Filament\Forms\Components\Hidden::make('status_date')
                        ->default(now()->toDateString()),
                    \Filament\Forms\Components\Hidden::make('all_status_dates')
                        ->default('{}'),
                ])
                ->columns(1),
        ];
    }
    public static function getNavigationLabel(): string    {
        return tr('sidebar.recruitment_housing.accommodation_entries', [], null, 'dashboard') ?: 'ادخالات الايواء';
    }

    public function getTitle(): string
    {
        return tr('housing.accommodation.create', [], null, 'dashboard') ?: 'إضافة إدخال إيواء جديد';
    }

    public function getHeading(): string
    {
        return tr('housing.accommodation.create', [], null, 'dashboard') ?: 'إضافة إدخال إيواء جديد';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.accommodation_entries.create') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getForms(): array
    {
        return ['form'];
    }

    public function form(Form $form): Form
    {
        $section = static::getUserSection();

        // Single-section users go straight in — no tabs overhead
        if ($section === 'complaints') {
            return $form->schema($this->buildSchema(false))->statePath('data');
        }

        if ($section === 'coordination') {
            return $form->schema($this->buildSchema(true))->statePath('data');
        }

        // Admins / owners see both tabs
        return $form
            ->schema([
                Tabs::make('sections')
                    ->tabs([
                        Tab::make('قسم الشكاوي')
                            ->icon('heroicon-o-chat-bubble-left-ellipsis')
                            ->schema($this->buildSchema(false)),

                        Tab::make('قسم التنسيق')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema($this->buildSchema(true)),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function viewContractDetails(): void
    {
        $contractNo = $this->data['contract_no'] ?? null;
        if (!$contractNo) {
            Notification::make()->title('لم يتم اختيار عقد')->warning()->send();
            return;
        }

        $contract = RecruitmentContract::with(['client', 'worker', 'nationality', 'profession'])
            ->where('contract_no', $contractNo)
            ->first();

        if (!$contract) {
            Notification::make()->title('العقد غير موجود')->danger()->send();
            return;
        }

        $statusLabels = [
            'new'        => 'جديد',
            'processing' => 'قيد المعالجة',
            'received'   => 'مستلم',
            'cancelled'  => 'ملغي',
            'completed'  => 'مكتمل',
        ];

        $this->contractDetails = [
            'contract_no'                       => $contract->contract_no,
            'status'                            => $statusLabels[$contract->status] ?? $contract->status,
            'gregorian_request_date'            => $contract->gregorian_request_date?->format('Y-m-d'),
            'musaned_contract_no'               => $contract->musaned_contract_no,
            'musaned_documentation_contract_no' => $contract->musaned_documentation_contract_no,
            'musaned_auth_no'                   => $contract->musaned_auth_no,
            'musaned_contract_date'             => $contract->musaned_contract_date?->format('Y-m-d'),
            'visa_no'                           => $contract->visa_no,
            'visa_date'                         => $contract->visa_date?->format('Y-m-d'),
            'arrival_date'                      => $contract->arrival_date?->format('Y-m-d'),
            'trial_end_date'                    => $contract->trial_end_date?->format('Y-m-d'),
            'contract_end_date'                 => $contract->contract_end_date?->format('Y-m-d'),
            'monthly_salary'                    => $contract->monthly_salary,
            'total_cost'                        => $contract->total_cost,
            'paid_total'                        => $contract->paid_total,
            'remaining_total'                   => $contract->remaining_total,
            'client_name'                       => $contract->client?->name_ar,
            'client_national_id'                => $contract->client?->national_id,
            'client_mobile'                     => $contract->client?->mobile,
            'worker_name'                       => $contract->worker?->name_ar,
            'worker_passport'                   => $contract->worker?->passport_number,
            'worker_nationality'                => $contract->nationality?->name_ar,
            'worker_profession'                 => $contract->profession?->name_ar,
            'worker_gender'                     => $contract->gender === 'female' ? 'أنثى' : ($contract->gender === 'male' ? 'ذكر' : null),
        ];

        $this->showContractModal = true;
    }

    public function closeContractModal(): void
    {
        $this->showContractModal = false;
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Parse the all_status_dates JSON map and remove it from entry data
        $allStatusDates = json_decode($data['all_status_dates'] ?? '{}', true) ?? [];
        unset($data['all_status_dates']);
        unset($data['status_date']);

        $data['type'] = 'recruitment';

        $entry = \App\Models\Housing\AccommodationEntry::create($data);

        // Persist a log row for every status that has a date filled in
        foreach ($allStatusDates as $statusKey => $statusDate) {
            if (!empty($statusKey) && !empty($statusDate)) {
                \App\Models\Housing\AccommodationEntryStatusLog::create([
                    'accommodation_entry_id' => $entry->id,
                    'old_status_id'          => null,
                    'new_status_id'          => null,
                    'status_key'             => $statusKey,
                    'status_date'            => $statusDate,
                    'created_by'             => auth()->id(),
                ]);
            }
        }

        Notification::make()
            ->title(tr('messages.saved_successfully', [], null, 'dashboard') ?: 'تم الحفظ بنجاح')
            ->success()
            ->send();

        $this->form->fill();
    }
}
