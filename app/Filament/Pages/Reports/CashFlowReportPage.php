<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Filament\Concerns\AccountingModuleGate;
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
    use InteractsWithForms, AccountingModuleGate;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.reports.cash-flow-report';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.cash_flow.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.cash_flow.title', [], null, 'dashboard');
    }

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
        $index = 0;

        foreach ($rows as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as account_code, ? as date, ? as entry_number, ? as reference, ? as description, ? as debit, ? as credit', [
                $index++,
                $row['account_code'] ?? '',
                $row['date'] ?? '',
                $row['entry_number'] ?? '',
                $row['reference'] ?? '',
                $row['description'] ?? '',
                $row['debit'] ?? 0,
                $row['credit'] ?? 0,
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw('0 as id, NULL as account_code, NULL as date, NULL as entry_number, NULL as reference, NULL as description, 0 as debit, 0 as credit');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\Voucher::query()
                ->fromSub($unionQuery, 'cash_flow_data')
                ->select('cash_flow_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('account_code')
                    ->label(trans_dash('reports.cash_flow.account_code', 'Account Code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(trans_dash('reports.cash_flow.date', 'Date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('entry_number')
                    ->label(trans_dash('reports.cash_flow.entry_number', 'Entry Number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label(trans_dash('reports.cash_flow.reference', 'Reference'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(trans_dash('reports.cash_flow.description', 'Description'))
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('debit')
                    ->label(trans_dash('reports.cash_flow.debit', 'Debit'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable(),
                Tables\Columns\TextColumn::make('credit')
                    ->label(trans_dash('reports.cash_flow.credit', 'Credit'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable(),
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

