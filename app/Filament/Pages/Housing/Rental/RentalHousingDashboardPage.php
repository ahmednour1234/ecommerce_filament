<?php

namespace App\Filament\Pages\Housing\Rental;

use App\Filament\Concerns\TranslatableNavigation;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Number;

class RentalHousingDashboardPage extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatableNavigation;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'rental_housing';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'sidebar.rental_housing.dashboard';
    protected static string $view = 'filament.pages.housing.rental.dashboard';

    public ?string $request_type = null;
    public ?string $status = null;
    public ?string $from_date = null;
    public ?string $to_date = null;

    public static function getNavigationLabel(): string
    {
        return tr('sidebar.rental_housing.dashboard', [], null, 'dashboard') ?: 'لوحة التحكم';
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
        return false;
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
        return \App\Models\Housing\HousingRequest::rental()
            ->where('status', 'completed')
            ->count();
    }

    public function getApprovedCount(): int
    {
        return \App\Models\Housing\HousingRequest::rental()
            ->where('status', 'approved')
            ->count();
    }

    public function getPendingCount(): int
    {
        return \App\Models\Housing\HousingRequest::rental()
            ->where('status', 'pending')
            ->count();
    }

    public function search(): void
    {
        $this->dispatch('$refresh');
    }

    public function resetFilters(): void
    {
        $this->request_type = null;
        $this->status = null;
        $this->from_date = null;
        $this->to_date = null;
        $this->form->fill();
    }
}
