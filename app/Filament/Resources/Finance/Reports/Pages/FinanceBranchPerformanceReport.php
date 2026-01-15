<?php

namespace App\Filament\Resources\Finance\Reports\Pages;

use App\Filament\Resources\Finance\Reports\Concerns\HasFinanceReportFilters;
use App\Filament\Resources\Finance\Reports\Widgets\TopBranchesNetBarChart;
use App\Models\Finance\BranchTransaction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;

class FinanceBranchPerformanceReport extends Page implements HasForms
{
    use InteractsWithForms;
    use HasFinanceReportFilters;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 51;

    protected static ?string $title = 'Finance Reports - Branch Performance';
    protected static ?string $navigationLabel = 'Reports (Branches)';

    protected static string $view = 'filament.finance.reports.branch-performance-report';

    public function mount(): void
    {
        $this->initDefaultDates();
        $this->form->fill([
            'from' => $this->from,
            'to'   => $this->to,
            'group_by' => $this->group_by,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $this->filtersForm($form);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TopBranchesNetBarChart::class,
        ];
    }

    protected function getWidgetData(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'country_id' => $this->country_id,
            'currency_id' => $this->currency_id,
            'status' => $this->status,
        ];
    }

    public function kpis(): array
    {
        [$from, $to] = $this->dateRange();

        $q = BranchTransaction::query()
            ->whereBetween('transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_id', auth()->user()?->branch_id);
        }

        $q->when($this->country_id, fn($qq) => $qq->where('country_id', $this->country_id));
        $q->when($this->currency_id, fn($qq) => $qq->where('currency_id', $this->currency_id));
        $q->when($this->status, fn($qq) => $qq->where('status', $this->status));

        $branchesCount = (clone $q)->distinct('branch_id')->count('branch_id');
        $txCount = (clone $q)->count();

        return [
            'branches' => $branchesCount,
            'tx' => $txCount,
        ];
    }
}
