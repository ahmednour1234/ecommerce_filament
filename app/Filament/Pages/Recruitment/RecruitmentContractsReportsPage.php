<?php

namespace App\Filament\Pages\Recruitment;

use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Recruitment\RecruitmentContract;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class RecruitmentContractsReportsPage extends Page implements HasForms
{
    use TranslatableNavigation;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'recruitment_contracts';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationTranslationKey = 'sidebar.recruitment_contracts.contracts_reports';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('recruitment_contracts.view_any') ?? false;
    }
    protected static string $view = 'filament.pages.recruitment.reports';

    protected static ?string $title = null;

    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?int $branchId = null;

    public static function getNavigationLabel(): string
    {
        return tr('recruitment_contract.menu.reports', [], null, 'dashboard') ?: 'التقارير';
    }

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');

        $this->form->fill([
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'branchId' => $this->branchId,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\DatePicker::make('dateFrom')
                    ->label(tr('common.date_from', [], null, 'dashboard') ?: 'From Date')
                    ->required()
                    ->default(now()->startOfMonth())
                    ->reactive(),

                \Filament\Forms\Components\DatePicker::make('dateTo')
                    ->label(tr('common.date_to', [], null, 'dashboard') ?: 'To Date')
                    ->required()
                    ->default(now())
                    ->reactive(),

                \Filament\Forms\Components\Select::make('branchId')
                    ->label(tr('recruitment_contract.fields.branch', [], null, 'dashboard') ?: 'Branch')
                    ->options(\App\Models\MainCore\Branch::active()->pluck('name', 'id'))
                    ->searchable()
                    ->reactive(),
            ]);
    }

    public function getStatsProperty(): array
    {
        $formData = $this->form->getState();
        $dateFrom = $formData['dateFrom'] ?? $this->dateFrom ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo = $formData['dateTo'] ?? $this->dateTo ?? now()->format('Y-m-d');
        $branchId = $formData['branchId'] ?? $this->branchId;

        $query = RecruitmentContract::query()
            ->whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $total = $query->count();
        $totalCost = $query->sum('total_cost');
        $paidTotal = $query->sum('paid_total');
        $remainingTotal = $query->sum('remaining_total');
        $received = (clone $query)->where('status', 'worker_received')->count();
        $closed = (clone $query)->where('status', 'closed')->count();

        return [
            'total' => $total,
            'total_cost' => $totalCost ?? 0,
            'paid_total' => $paidTotal ?? 0,
            'remaining_total' => $remainingTotal ?? 0,
            'received' => $received,
            'closed' => $closed,
        ];
    }
}
