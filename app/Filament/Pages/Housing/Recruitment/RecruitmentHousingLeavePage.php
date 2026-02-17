<?php

namespace App\Filament\Pages\Housing\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\HR\Employee;
use App\Models\HR\LeaveType;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class RecruitmentHousingLeavePage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'recruitment_housing';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_housing.laborer_leaves';
    protected static string $view = 'filament.pages.housing.create-leave';

    public ?int $employee_id = null;
    public ?int $leave_type_id = null;
    public ?string $start_date = null;
    public ?int $days = null;
    public ?string $end_date = null;
    public ?string $status = 'pending';
    public ?string $reason = null;
    public ?string $notes = null;

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.recruitment_housing.laborer_leaves', [], null, 'dashboard') ?: 'إجازات العمالة';
    }

    public function getTitle(): string
    {
        return tr('housing.leave.create', [], null, 'dashboard') ?: 'إضافة إجازة جديدة';
    }

    public function getHeading(): string
    {
        return tr('housing.leave.create', [], null, 'dashboard') ?: 'إضافة إجازة جديدة';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.leaves.create') ?? false;
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
                \Filament\Forms\Components\Section::make(tr('housing.leave.create', [], null, 'dashboard') ?: 'إضافة إجازة جديدة')
                    ->schema([
                        \Filament\Forms\Components\Select::make('employee_id')
                            ->label(tr('housing.leave.employee', [], null, 'dashboard') ?: 'اسم العامل')
                            ->options(function () {
                                return Employee::query()
                                    ->get()
                                    ->mapWithKeys(fn ($employee) => [
                                        $employee->id => $employee->first_name . ' ' . $employee->last_name
                                    ])
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->reactive()
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('leave_type_id')
                            ->label(tr('housing.leave.leave_type', [], null, 'dashboard') ?: 'نوع الإجازة')
                            ->options(function () {
                                return LeaveType::where('status', 'active')
                                    ->get()
                                    ->mapWithKeys(fn ($type) => [
                                        $type->id => app()->getLocale() === 'ar' ? $type->name_ar : $type->name_en
                                    ])
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->columnSpan(1),

                        \Filament\Forms\Components\DatePicker::make('start_date')
                            ->label(tr('housing.leave.start_date', [], null, 'dashboard') ?: 'تاريخ بداية الإجازة')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateEndDate())
                            ->columnSpan(1),

                        \Filament\Forms\Components\TextInput::make('days')
                            ->label(tr('housing.leave.days', [], null, 'dashboard') ?: 'عدد الأيام')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->calculateEndDate())
                            ->columnSpan(1),

                        \Filament\Forms\Components\DatePicker::make('end_date')
                            ->label(tr('housing.leave.end_date', [], null, 'dashboard') ?: 'تاريخ نهاية الإجازة')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        \Filament\Forms\Components\Select::make('status')
                            ->label(tr('housing.leave.status', [], null, 'dashboard') ?: 'حالة الإجازة')
                            ->options([
                                'pending' => tr('housing.leave.status.pending', [], null, 'dashboard') ?: 'معلقة',
                                'approved' => tr('housing.dashboard.approved_requests', [], null, 'dashboard') ?: 'موافق عليها',
                                'rejected' => tr('actions.reject', [], null, 'dashboard') ?: 'مرفوضة',
                            ])
                            ->default('pending')
                            ->required()
                            ->columnSpan(1),

                        \Filament\Forms\Components\Textarea::make('reason')
                            ->label(tr('housing.leave.reason', [], null, 'dashboard') ?: 'سبب الإجازة')
                            ->rows(3)
                            ->columnSpanFull(),

                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label(tr('housing.leave.notes', [], null, 'dashboard') ?: 'ملاحظات')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function calculateEndDate(): void
    {
        if ($this->start_date && $this->days) {
            try {
                $start = Carbon::parse($this->start_date);
                $end = $start->copy()->addDays($this->days - 1);
                $this->end_date = $end->format('Y-m-d');
            } catch (\Exception $e) {
                $this->end_date = null;
            }
        } else {
            $this->end_date = null;
        }
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['end_date'] = $this->end_date;
        $data['type'] = 'recruitment';

        \App\Models\Housing\HousingLeave::create($data);

        Notification::make()
            ->title(tr('messages.saved_successfully', [], null, 'dashboard') ?: 'تم الحفظ بنجاح')
            ->success()
            ->send();

        $this->form->fill();
        $this->reset(['end_date']);
    }
}
