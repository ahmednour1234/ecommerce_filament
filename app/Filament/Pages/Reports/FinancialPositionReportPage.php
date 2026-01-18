<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Filament\Concerns\AccountingModuleGate;
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
    use InteractsWithForms, AccountingModuleGate;

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
        $index = 0;

        foreach ($rows as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as branch, ? as cost_center, ? as total_debit, ? as total_credit, ? as balance', [
                $index++,
                $row['branch'] ?? '',
                $row['cost_center'] ?? '',
                $row['total_debit'] ?? 0,
                $row['total_credit'] ?? 0,
                $row['balance'] ?? 0,
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw('0 as id, NULL as branch, NULL as cost_center, 0 as total_debit, 0 as total_credit, 0 as balance');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\GeneralLedgerEntry::query()
                ->fromSub($unionQuery, 'financial_position_data')
                ->select('financial_position_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('branch')
                    ->label(trans_dash('reports.financial_position.branch', 'Branch')),
                Tables\Columns\TextColumn::make('cost_center')
                    ->label(trans_dash('reports.financial_position.cost_center', 'Cost Center')),
                Tables\Columns\TextColumn::make('total_debit')
                    ->label(trans_dash('reports.financial_position.total_debit', 'Total Debit'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('total_credit')
                    ->label(trans_dash('reports.financial_position.total_credit', 'Total Credit'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('balance')
                    ->label(trans_dash('reports.financial_position.balance', 'Balance'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
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

