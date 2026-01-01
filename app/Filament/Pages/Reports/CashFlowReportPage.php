<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\CashFlowReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class CashFlowReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.reports.cash-flow-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfMonth()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
            'posted_only' => true,
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
        $service = new CashFlowReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('vouchers')
                ->whereRaw('1 = 0')
                ->selectRaw('? as date, ? as type, ? as voucher_number, ? as description, ? as cash_in, ? as cash_out', [
                    $row['date'] ?? '',
                    $row['type'] ?? '',
                    $row['voucher_number'] ?? '',
                    $row['description'] ?? '',
                    $row['cash_in'] ?? 0,
                    $row['cash_out'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('vouchers')->whereRaw('1 = 0')
                ->selectRaw('NULL as date, NULL as type, NULL as voucher_number, NULL as description, 0 as cash_in, 0 as cash_out');
        }

        $query = \App\Models\Accounting\Voucher::query()
            ->fromSub($unionQuery, 'cash_flow_data')
            ->select('cash_flow_data.*');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('voucher_number'),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('cash_in')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('cash_out')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->defaultSort('date', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'cash-flow', 'filters' => $this->data]),
            fn () => (new CashFlowReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new CashFlowReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.cash_flow', 'Cash Flow');
    }
}

