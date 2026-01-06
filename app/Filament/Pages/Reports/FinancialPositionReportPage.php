<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\FinancialPositionReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class FinancialPositionReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 13;
    protected static string $view = 'filament.pages.reports.financial-position-report';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.financial_position.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.financial_position.title', [], null, 'dashboard');
    }

    public function mount(): void
    {
        $this->form->fill([
            'to_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                ReportFilters::section([
                    'requireDateRange' => false,
                    'showAccount' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO($this->data);
        $service = new FinancialPositionReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('general_ledger_entries')
                ->whereRaw('1 = 0')
                ->selectRaw('? as branch, ? as cost_center, ? as total_debit, ? as total_credit, ? as balance', [
                    $row['branch'] ?? '',
                    $row['cost_center'] ?? '',
                    $row['total_debit'] ?? 0,
                    $row['total_credit'] ?? 0,
                    $row['balance'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('general_ledger_entries')->whereRaw('1 = 0')
                ->selectRaw('NULL as branch, NULL as cost_center, 0 as total_debit, 0 as total_credit, 0 as balance');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\GeneralLedgerEntry::query()
                ->fromSub($unionQuery, 'financial_position_data')
                ->select('financial_position_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('branch'),
                Tables\Columns\TextColumn::make('cost_center'),
                Tables\Columns\TextColumn::make('total_debit')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('total_credit')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('balance')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->emptyStateHeading(tr('pages.reports.financial_position.empty_state', [], null, 'dashboard'))
            ->defaultSort('branch', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'financial-position', 'filters' => $this->data]),
            fn () => (new FinancialPositionReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new FinancialPositionReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.financial_position', 'Financial Position');
    }
}

