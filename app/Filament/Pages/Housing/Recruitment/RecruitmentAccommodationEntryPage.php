<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Laborer;
use App\Models\Recruitment\Nationality;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class RecruitmentAccommodationEntryPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'إيواء الاستقدام';
    protected static ?string $navigationLabel = 'ادخالات الايواء';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_housing.accommodation_entries';
    protected static string $view = 'filament.pages.housing.create-accommodation-entry';

    public ?int $laborer_id = null;
    public ?string $contract_no = null;
    public ?string $entry_type = null;
    public ?string $entry_date = null;
    public ?string $exit_date = null;
    public ?int $status_id = null;
    public ?int $building_id = null;
    public ?string $new_sponsor_name = null;
    public ?string $old_sponsor_name = null;
    public ?int $nationality_id = null;
    public ?string $worker_passport_number = null;
    public ?string $new_sponsor_phone = null;
    public ?string $old_sponsor_phone = null;

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
                \Filament\Forms\Components\Section::make(tr('housing.accommodation.create', [], null, 'dashboard') ?: 'إضافة إدخال إيواء جديد')
                    ->schema([
                        \Filament\Forms\Components\Select::make('laborer_id')
                            ->label(tr('housing.accommodation.laborer', [], null, 'dashboard') ?: 'العامل')
                            ->options(function () {
                                return Laborer::query()
                                    ->get()
                                    ->mapWithKeys(fn ($laborer) => [
                                        $laborer->id => app()->getLocale() === 'ar' ? $laborer->name_ar : $laborer->name_en
                                    ])
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

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
                                return \App\Models\Housing\Building::available()
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

                        \Filament\Forms\Components\TextInput::make('new_sponsor_name')
                            ->label('اسم الكفيل الجديد')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('old_sponsor_name')
                            ->label('اسم الكفيل القديم')
                            ->columnSpan(1),

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
                            ->label('رقم جواز العامل')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('new_sponsor_phone')
                            ->label('رقم جوال الكفيل الجديد')
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('old_sponsor_phone')
                            ->label('رقم جوال الكفيل القديم')
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
