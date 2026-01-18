<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Filament\Concerns\AccountingModuleGate;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\IncomeStatementReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

/**
 * Income Statement Report Page
 */
class IncomeStatementReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms, AccountingModuleGate;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.reports.income-statement-report';

    public ?array $data = [];
    protected ?IncomeStatementReportService $reportService = null;

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.income_statement.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.income_statement.title', [], null, 'dashboard');
    }

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfMonth()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
            'posted_only' => true,
            'include_zero_rows' => false,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                ReportFilters::section([
                    'requireDateRange' => true,
                    'showAccount' => false,
                    'showCurrency' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO($this->data);
        $this->reportService = new IncomeStatementReportService($filters);
        $reportData = $this->reportService->getData();

        // Build query for table
        $rows = $reportData->rows;
        $unionQueries = [];
        $index = 0;

        foreach ($rows as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as section, ? as account_code, ? as account_name, ? as amount', [
                $index++,
                $row['section'] ?? '',
                $row['account_code'] ?? '',
                $row['account_name'] ?? '',
                $row['amount'] ?? null,
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            if ($unionQuery === null) {
                $unionQuery = $uq;
            } else {
                $unionQuery->unionAll($uq);
            }
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw('0 as id, NULL as section, NULL as account_code, NULL as account_name, NULL as amount');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\Account::query()
                ->fromSub($unionQuery, 'income_statement_data')
                ->select('income_statement_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('account_code')
                    ->label(trans_dash('reports.income_statement.account_code', 'Account Code'))
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\TextColumn::make('account_name')
                    ->label(trans_dash('reports.income_statement.account_name', 'Account Name'))
                    ->searchable(false)
                    ->sortable(false)
                    ->formatStateUsing(function ($record) {
                        $name = $record->account_name ?? '';
                        $section = $record->section ?? '';
                        
                        if ($section === 'header') {
                            return '<strong style="font-size: 1.1em;">' . $name . '</strong>';
                        }
                        if ($section === 'total' || $section === 'net') {
                            return '<strong>' . $name . '</strong>';
                        }
                        return '&nbsp;&nbsp;&nbsp;' . $name; // Indent detail rows
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(trans_dash('reports.income_statement.amount', 'Amount'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable(false)
                    ->formatStateUsing(function ($record) {
                        if ($record->section === 'header' || $record->amount === null) {
                            return '';
                        }
                        return $record->amount ?? 0;
                    })
                    ->color(function ($record) {
                        if ($record->section === 'net') {
                            return ($record->amount ?? 0) >= 0 ? 'success' : 'danger';
                        }
                        return null;
                    })
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(\App\Support\Money::defaultCurrencyCode()),
                    ]),
            ])
            ->defaultSort('account_code', 'asc')
            ->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'income-statement', 'filters' => $this->data]),
            fn () => $this->exportPdf(),
            fn () => $this->exportExcel()
        );
    }

    protected function exportPdf()
    {
        $filters = new FilterDTO($this->data);
        $service = new IncomeStatementReportService($filters);
        return $service->exportPdf();
    }

    protected function exportExcel()
    {
        $filters = new FilterDTO($this->data);
        $service = new IncomeStatementReportService($filters);
        return $service->exportExcel();
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.income_statement', 'Income Statement');
    }
}

