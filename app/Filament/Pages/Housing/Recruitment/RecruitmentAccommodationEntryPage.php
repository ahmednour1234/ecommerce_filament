<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use App\Models\Recruitment\Profession;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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

    public ?int $laborer_id = null;
    public ?string $contract_no = null;
    public ?string $entry_type = null;
    public ?string $entry_date = null;
    public ?string $exit_date = null;
    public ?int $status_id = null;
    public ?int $building_id = null;
    public ?int $nationality_id = null;
    public ?string $worker_passport_number = null;
    public ?string $customer_name = null;
    public ?string $customer_phone = null;
    public ?string $customer_id_number = null;

    public static function getNavigationLabel(): string
    {
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
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('بيانات العاملة')
                    ->schema([
                        \Filament\Forms\Components\Select::make('laborer_id')
                            ->label(tr('housing.accommodation.laborer', [], null, 'dashboard') ?: 'العاملة')
                            ->options(function () {
                                return Cache::remember('recruitment_accommodation.workers', 21600, function () {
                                    return Laborer::where('is_available', true)
                                        ->get()
                                        ->mapWithKeys(function ($worker) {
                                            return [$worker->id => "{$worker->name_ar} ({$worker->passport_number})"];
                                        })
                                        ->toArray();
                                });
                            })
                            ->required()
                            ->searchable()
                            ->createOptionForm([
                                \Filament\Forms\Components\TextInput::make('name_ar')
                                    ->label(tr('recruitment.fields.name_ar', [], null, 'dashboard') ?: 'الاسم (عربي)')
                                    ->required()
                                    ->maxLength(255),
                                \Filament\Forms\Components\TextInput::make('passport_number')
                                    ->label(tr('recruitment.fields.passport_number', [], null, 'dashboard') ?: 'رقم الجواز')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Laborer::class, 'passport_number'),
                                \Filament\Forms\Components\Select::make('nationality_id')
                                    ->label(tr('recruitment.fields.nationality', [], null, 'dashboard') ?: 'الجنسية')
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
                                \Filament\Forms\Components\Select::make('profession_id')
                                    ->label(tr('recruitment.fields.profession', [], null, 'dashboard') ?: 'المهنة')
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
                                \Filament\Forms\Components\Select::make('gender')
                                    ->label(tr('recruitment.fields.gender', [], null, 'dashboard') ?: 'الجنس')
                                    ->options([
                                        'male' => tr('recruitment_contract.gender.male', [], null, 'dashboard') ?: 'ذكر',
                                        'female' => tr('recruitment_contract.gender.female', [], null, 'dashboard') ?: 'أنثى',
                                    ])
                                    ->nullable(),
                                \Filament\Forms\Components\Select::make('experience')
                                    ->label(tr('recruitment.fields.experience', [], null, 'dashboard') ?: 'الخبرة')
                                    ->options([
                                        'unspecified' => tr('recruitment_contract.experience.unspecified', [], null, 'dashboard') ?: 'غير محدد',
                                        'new' => tr('recruitment_contract.experience.new', [], null, 'dashboard') ?: 'جديد',
                                        'ex_worker' => tr('recruitment_contract.experience.ex_worker', [], null, 'dashboard') ?: 'سبق العمل EX',
                                    ])
                                    ->nullable(),
                                \Filament\Forms\Components\TextInput::make('phone_1')
                                    ->label(tr('recruitment.fields.phone_1', [], null, 'dashboard') ?: 'الجوال')
                                    ->tel()
                                    ->maxLength(50)
                                    ->nullable(),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $laborer = Laborer::create([
                                    'name_ar' => $data['name_ar'],
                                    'passport_number' => $data['passport_number'],
                                    'nationality_id' => $data['nationality_id'],
                                    'profession_id' => $data['profession_id'],
                                    'gender' => $data['gender'] ?? null,
                                    'experience_level' => $data['experience'] ?? null,
                                    'phone_1' => $data['phone_1'] ?? null,
                                    'is_available' => true,
                                ]);
                                Cache::forget('recruitment_accommodation.workers');
                                return $laborer->id;
                            })
                            ->columnSpan(1)
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $laborer = Laborer::find($state);
                                    if ($laborer) {
                                        $set('worker_passport_number', $laborer->passport_number);
                                        $set('nationality_id', $laborer->nationality_id);
                                    }
                                }
                            }),

                        \Filament\Forms\Components\Select::make('nationality_id')
                            ->label('الجنسية')
                            ->options(function () {
                                $allowedNames = ['الفلبين', 'بنغلادش', 'سريلانكا', 'اثيوبيا', 'اوغندا', 'كينيا', 'بورندي'];
                                return Nationality::where('is_active', true)
                                    ->whereIn('name_ar', $allowedNames)
                                    ->get()
                                    ->mapWithKeys(function ($nationality) {
                                        $label = app()->getLocale() === 'ar' ? $nationality->name_ar : $nationality->name_en;
                                        return [$nationality->id => $label];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('worker_passport_number')
                            ->label('رقم جواز العاملة')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Forms\Components\Section::make('بيانات العميل')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('customer_name')
                            ->label('اسم العميل')
                            ->maxLength(255)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('customer_phone')
                            ->label('رقم جوال العميل')
                            ->tel()
                            ->maxLength(50)
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('customer_id_number')
                            ->label('رقم هوية العميل')
                            ->maxLength(255)
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                \Filament\Forms\Components\Section::make('بيانات الإدخال')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('contract_no')
                            ->label(tr('housing.accommodation.contract_no', [], null, 'dashboard') ?: 'رقم العقد')
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('entry_type')
                            ->label(tr('housing.accommodation.entry_type', [], null, 'dashboard') ?: 'نوع الدخول')
                            ->options([
                                'new_arrival' => tr('housing.accommodation.entry_type.new_arrival', [], null, 'dashboard') ?: 'وافد جديد',
                                'return' => tr('housing.accommodation.entry_type.return', [], null, 'dashboard') ?: 'استرجاع',
                                'transfer' => tr('housing.accommodation.entry_type.transfer', [], null, 'dashboard') ?: 'نقل',
                            ])
                            ->required()
                            ->columnSpan(1),

                        \Filament\Forms\Components\DateTimePicker::make('entry_date')
                            ->label(tr('housing.accommodation.entry_date', [], null, 'dashboard') ?: 'تاريخ الدخول')
                            ->required()
                            ->native(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\DateTimePicker::make('exit_date')
                            ->label(tr('housing.accommodation.exit_date', [], null, 'dashboard') ?: 'تاريخ خروج')
                            ->native(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('status_id')
                            ->label(tr('housing.accommodation.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options(function () {
                                return \App\Models\Housing\HousingStatus::active()
                                    ->ordered()
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('building_id')
                            ->label(tr('housing.accommodation.building', [], null, 'dashboard') ?: 'المبنى')
                            ->options(function () {
                                return \App\Models\Housing\Building::availableRecruitment()
                                    ->get()
                                    ->mapWithKeys(fn ($building) => [
                                        $building->id => $building->name . ' (' . $building->available_capacity . ' ' . tr('common.available', [], null, 'dashboard') . ')'
                                    ])
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->helperText(tr('housing.accommodation.available_buildings_note', [], null, 'dashboard') ?: 'يتم عرض المباني المتاحة فقط (السعة المتاحة > 0)')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['type'] = 'recruitment';

        \App\Models\Housing\AccommodationEntry::create($data);

        Notification::make()
            ->title(tr('messages.saved_successfully', [], null, 'dashboard') ?: 'تم الحفظ بنجاح')
            ->success()
            ->send();

        $this->form->fill();
    }
}
