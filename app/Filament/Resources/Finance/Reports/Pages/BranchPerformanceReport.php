<?php

namespace App\Filament\Resources\Finance\Reports\Pages;

use App\Filament\Concerns\ExportsResourceTable;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Resources\Finance\Reports\Concerns\HasFinanceReportFilters;
use App\Filament\Resources\Finance\Reports\Widgets\TopBranchesNetBarChart;
use App\Models\Finance\BranchTransaction;
use Filament\Actions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchPerformanceReport extends Page implements HasForms, HasTable
{
    use TranslatableNavigation;
    use InteractsWithForms;
    use InteractsWithTable;
    use HasFinanceReportFilters;
    use ExportsResourceTable;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 61;

    protected static ?string $navigationTranslationKey = 'sidebar.finance.reports.branches';
    protected static ?string $title = 'Branch Performance Report';

    protected static string $view = 'filament.finance.reports.branch-performance-report';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('finance_reports.view'), 403);

        $this->initDefaultDates();
        $this->form->fill([
            'from' => $this->from,
            'to' => $this->to,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        // هنا مش محتاج group_by
        return $this->filtersForm($form);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label(tr('actions.export_excel', [], null, 'dashboard'))
                ->visible(fn () => auth()->user()?->can('finance_reports.export'))
                ->action(fn () => $this->exportToExcel()),

            Actions\Action::make('export_pdf')
                ->label(tr('actions.export_pdf', [], null, 'dashboard'))
                ->visible(fn () => auth()->user()?->can('finance_reports.export'))
                ->action(fn () => $this->exportToPdf()),

            Actions\Action::make('print')
                ->label(tr('actions.print', [], null, 'dashboard'))
                ->visible(fn () => auth()->user()?->can('finance_reports.print'))
                ->url(fn () => $this->getPrintUrl())
                ->openUrlInNewTab(),
        ];
    }

    protected function getExportTitle(): ?string
    {
        return tr('reports.branches.title', [], null, 'dashboard');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->reportQuery())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('branch_name')
                    ->label(tr('reports.columns.branch', [], null, 'dashboard'))
                    ->sortable()
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('income')
                    ->label(tr('reports.columns.income', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($s) => number_format((float) $s, 2))
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('expense')
                    ->label(tr('reports.columns.expense', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($s) => number_format((float) $s, 2))
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('net')
                    ->label(tr('reports.columns.net', [], null, 'dashboard'))
                    ->formatStateUsing(fn ($s) => number_format((float) $s, 2))
                    ->sortable(),
            ])
            ->defaultSort('net', 'desc')
            ->paginated(false);
    }

    protected function reportQuery(): Builder
    {
        [$from, $to] = $this->dateRange();

        $q = BranchTransaction::query()
            ->join('branches', 'branch_transactions.branch_id', '=', 'branches.id')
            ->whereBetween('branch_transactions.transaction_date', [$from, $to]);

        if (! auth()->user()?->can('branch_tx.view_all_branches')) {
            $q->where('branch_transactions.branch_id', auth()->user()?->branch_id);
        }

        $q->when($this->branch_id, fn ($qq) => $qq->where('branch_transactions.branch_id', $this->branch_id));
        $q->when($this->country_id, fn ($qq) => $qq->where('branch_transactions.country_id', $this->country_id));
        $q->when($this->currency_id, fn ($qq) => $qq->where('branch_transactions.currency_id', $this->currency_id));
        $q->when($this->status, fn ($qq) => $qq->where('branch_transactions.status', $this->status));

        return $q->selectRaw("
                branch_transactions.branch_id,
                branches.name as branch_name,
                SUM(CASE WHEN branch_transactions.type='income' THEN branch_transactions.amount ELSE 0 END) as income,
                SUM(CASE WHEN branch_transactions.type='expense' THEN branch_transactions.amount ELSE 0 END) as expense,
                (SUM(CASE WHEN branch_transactions.type='income' THEN branch_transactions.amount ELSE 0 END) - SUM(CASE WHEN branch_transactions.type='expense' THEN branch_transactions.amount ELSE 0 END)) as net
            ")
            ->groupBy('branch_transactions.branch_id', 'branches.name')
            ->orderByDesc('net');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TopBranchesNetBarChart::class,
        ];
    }

    public function getWidgetData(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'country_id' => $this->country_id,
            'currency_id' => $this->currency_id,
            'status' => $this->status,
        ];
    }
}
