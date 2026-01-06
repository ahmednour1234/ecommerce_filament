<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\AccountStatementReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class AccountStatementReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.pages.reports.account-statement-report';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.account_statement.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.account_statement.title', [], null, 'dashboard');
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
                    'showAccount' => true,
                    'requireAccount' => true,
                    'showCurrency' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        // Don't build the query if account_id is not set
        if (empty($this->data['account_id'])) {
            // Filament Tables requires an Eloquent Builder, not a Query Builder.
            // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
            return $table
                ->query(fn () => \App\Models\Accounting\GeneralLedgerEntry::query()
                    ->whereRaw('1 = 0')
                    ->selectRaw('NULL as date, NULL as entry_number, NULL as reference, NULL as description, 0 as debit, 0 as credit, 0 as balance')
                )
                ->columns([
                    Tables\Columns\TextColumn::make('date')->date(),
                    Tables\Columns\TextColumn::make('entry_number'),
                    Tables\Columns\TextColumn::make('reference'),
                    Tables\Columns\TextColumn::make('description'),
                    Tables\Columns\TextColumn::make('debit')->money(\App\Support\Money::defaultCurrencyCode()),
                    Tables\Columns\TextColumn::make('credit')->money(\App\Support\Money::defaultCurrencyCode()),
                    Tables\Columns\TextColumn::make('balance')->money(\App\Support\Money::defaultCurrencyCode()),
                ])
                ->emptyStateHeading(trans_dash('reports.account_statement.select_account', 'Please select an account to view the statement'))
                ->emptyStateDescription(trans_dash('reports.account_statement.select_account_description', 'Choose an account from the filters above to generate the account statement.'))
                ->paginated(false);
        }

        $filters = new FilterDTO($this->data);
        $service = new AccountStatementReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('general_ledger_entries')
                ->whereRaw('1 = 0')
                ->selectRaw('? as date, ? as entry_number, ? as reference, ? as description, ? as debit, ? as credit, ? as balance', [
                    $row['date'] ?? '',
                    $row['entry_number'] ?? '',
                    $row['reference'] ?? '',
                    $row['description'] ?? '',
                    $row['debit'] ?? 0,
                    $row['credit'] ?? 0,
                    $row['balance'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('general_ledger_entries')->whereRaw('1 = 0')
                ->selectRaw('NULL as date, NULL as entry_number, NULL as reference, NULL as description, 0 as debit, 0 as credit, 0 as balance');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\GeneralLedgerEntry::query()
                ->fromSub($unionQuery, 'account_statement_data')
                ->select('account_statement_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('entry_number')->searchable(),
                Tables\Columns\TextColumn::make('reference')->searchable(),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('debit')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('credit')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('balance')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->defaultSort('date', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        // Only show export actions if account is selected
        if (empty($this->data['account_id'])) {
            return [];
        }

        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'account-statement', 'filters' => $this->data]),
            fn () => (new AccountStatementReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new AccountStatementReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.account_statement', 'Account Statement');
    }
}

