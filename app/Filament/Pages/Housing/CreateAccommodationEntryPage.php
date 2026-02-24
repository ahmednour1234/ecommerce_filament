<?php

namespace App\Filament\Pages\Housing;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\Laborer;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class CreateAccommodationEntryPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'الإيواء';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.housing_requests';
    protected static string $view = 'filament.pages.housing.create-accommodation-entry';

    public ?int $laborer_id = null;
    public ?string $contract_no = null;
    public ?string $entry_type = null;
    public ?string $entry_date = null;
    public ?string $status = null;
    public ?int $building_id = null;

    public static function getNavigationLabel(): string
    {
        return tr('housing.accommodation.create', [], null, 'dashboard') ?: 'إضافة إدخال إيواء جديد';
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
        return false; // Hidden - replaced by RecruitmentAccommodationEntryPage and RentalAccommodationEntryPage
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

                        \Filament\Forms\Components\Select::make('status')
                            ->label(tr('housing.accommodation.status', [], null, 'dashboard') ?: 'الحالة')
                            ->options([
                                'pending' => tr('housing.leave.status.pending', [], null, 'dashboard') ?: 'معلقة',
                                'active' => tr('common.active', [], null, 'dashboard') ?: 'نشط',
                                'completed' => tr('housing.dashboard.completed_requests', [], null, 'dashboard') ?: 'مكتمل',
                            ])
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
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        \App\Models\Housing\AccommodationEntry::create($data);

        Notification::make()
            ->title(tr('messages.saved_successfully', [], null, 'dashboard') ?: 'تم الحفظ بنجاح')
            ->success()
            ->send();

        $this->form->fill();
    }
}
