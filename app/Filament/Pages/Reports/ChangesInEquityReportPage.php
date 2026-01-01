<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\ChangesInEquityReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class ChangesInEquityReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 14;
    protected static string $view = 'filament.pages.reports.changes-in-equity-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfYear()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                ReportFilters::section([
                    'requireDateRange' => true,
                    'showAccount' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO($this->data);
        $service = new ChangesInEquityReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('general_ledger_entries')
                ->whereRaw('1 = 0')
                ->selectRaw('? as date, ? as account_code, ? as account_name, ? as description, ? as movement', [
                    $row['date'] ?? '',
                    $row['account_code'] ?? '',
                    $row['account_name'] ?? '',
                    $row['description'] ?? '',
                    $row['movement'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('general_ledger_entries')->whereRaw('1 = 0')
                ->selectRaw('NULL as date, NULL as account_code, NULL as account_name, NULL as description, 0 as movement');
        }

        $query = \App\Models\Accounting\GeneralLedgerEntry::query()
            ->fromSub($unionQuery, 'changes_in_equity_data')
            ->select('changes_in_equity_data.*');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('account_code'),
                Tables\Columns\TextColumn::make('account_name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('movement')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->defaultSort('date', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'changes-in-equity', 'filters' => $this->data]),
            fn () => (new ChangesInEquityReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new ChangesInEquityReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.changes_in_equity', 'Changes in Equity');
    }
}

