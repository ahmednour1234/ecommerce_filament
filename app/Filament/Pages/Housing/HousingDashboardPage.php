<?php

namespace App\Filament\Pages\Housing;

use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class HousingDashboardPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'housing';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.housing.dashboard';
    protected static string $view = 'filament.pages.housing.dashboard';

    public ?string $request_type = null;
    public ?string $status = null;
    public ?string $from_date = null;
    public ?string $to_date = null;

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.housing.dashboard', [], null, 'dashboard') ?: 'لوحة التحكم';
    }

    public function getTitle(): string
    {
        return tr('housing.dashboard.heading', [], null, 'dashboard') ?: 'لوحة تحكم الإيواء';
    }

    public function getHeading(): string
    {
        return tr('housing.dashboard.heading', [], null, 'dashboard') ?: 'لوحة تحكم الإيواء';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('housing.dashboard.view') ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getForms(): array
    {
        return ['form'];
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('request_type')
                    ->label(tr('housing.dashboard.request_type', [], null, 'dashboard') ?: 'نوع الطلب')
                    ->options([
                        'delivery' => tr('housing.requests.type.delivery', [], null, 'dashboard') ?: 'تسليم',
                        'return' => tr('housing.requests.type.return', [], null, 'dashboard') ?: 'استرجاع',
                    ])
                    ->columnSpan(1),

                \Filament\Forms\Components\Select::make('status')
                    ->label(tr('housing.dashboard.status', [], null, 'dashboard') ?: 'الحالة')
                    ->options([
                        'pending' => tr('housing.leave.status.pending', [], null, 'dashboard') ?: 'معلقة',
                        'approved' => tr('housing.dashboard.approved_requests', [], null, 'dashboard') ?: 'موافق عليها',
                        'completed' => tr('housing.dashboard.completed_requests', [], null, 'dashboard') ?: 'مكتملة',
                    ])
                    ->columnSpan(1),

                \Filament\Forms\Components\DatePicker::make('from_date')
                    ->label(tr('housing.dashboard.from_date', [], null, 'dashboard') ?: 'من تاريخ')
                    ->columnSpan(1),

                \Filament\Forms\Components\DatePicker::make('to_date')
                    ->label(tr('housing.dashboard.to_date', [], null, 'dashboard') ?: 'إلى تاريخ')
                    ->columnSpan(1),
            ])
            ->columns(4)
            ->statePath('data');
    }

    public function getCompletedCount(): int
    {
        // TODO: Replace with actual model query when models exist
        // return \App\Models\Housing\HousingRequest::where('status', 'completed')->count();
        return 0;
    }

    public function getApprovedCount(): int
    {
        // TODO: Replace with actual model query when models exist
        // return \App\Models\Housing\HousingRequest::where('status', 'approved')->count();
        return 0;
    }

    public function getPendingCount(): int
    {
        // TODO: Replace with actual model query when models exist
        // return \App\Models\Housing\HousingRequest::where('status', 'pending')->count();
        return 0;
    }

    public function search(): void
    {
        // TODO: Implement search logic
        $this->dispatch('$refresh');
    }

    public function reset(): void
    {
        $this->request_type = null;
        $this->status = null;
        $this->from_date = null;
        $this->to_date = null;
        $this->form->fill();
    }
}
