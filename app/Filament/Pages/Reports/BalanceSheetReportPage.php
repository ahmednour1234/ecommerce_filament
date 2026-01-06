<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\BalanceSheetReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class BalanceSheetReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 5;
    protected static string $view = 'filament.pages.reports.balance-sheet-report';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.balance_sheet.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.balance_sheet.title', [], null, 'dashboard');
    }

    public function mount(): void
    {
        $this->form->fill([
            'to_date' => now()->format('Y-m-d'),
            'posted_only' => true,
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                ReportFilters::section([
                    'requireDateRange' => false,
                    'showAccount' => false,
                    'showCurrency' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO($this->data);
        $service = new BalanceSheetReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];
        $index = 0;

        foreach ($rows as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as section, ? as account_code, ? as account_name, ? as balance', [
                $index++,
                $row['section'] ?? '',
                $row['account_code'] ?? '',
                $row['account_name'] ?? '',
                $row['balance'] ?? null,
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw('0 as id, NULL as section, NULL as account_code, NULL as account_name, NULL as balance');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Even though fromSub() accepts Query Builder, we wrap it in a closure
        // to ensure Filament receives a proper Eloquent Builder instance.
        // The fromSub() method allows us to use a union query built from DB::table()
        // while maintaining the Eloquent Builder type that Filament expects.
        return $table
            ->query(fn () => \App\Models\Accounting\Account::query()
                ->fromSub($unionQuery, 'balance_sheet_data')
                ->select('balance_sheet_data.*')
                ->withoutGlobalScopes() // Remove any global scopes that might add ordering
            )
            ->columns([
                Tables\Columns\TextColumn::make('account_code')
                    ->label(trans_dash('reports.balance_sheet.account_code', 'Account Code'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('account_name')
                    ->label(trans_dash('reports.balance_sheet.account_name', 'Account Name'))
                    ->formatStateUsing(fn ($record) => $record->account_name === 'TOTAL' ? '<strong>' . $record->account_name . '</strong>' : $record->account_name)
                    ->html(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(trans_dash('reports.balance_sheet.balance', 'Balance'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->formatStateUsing(fn ($record) => $record->balance !== null ? $record->balance : ''),
            ])
            ->defaultSort('account_code', 'asc')
            ->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'balance-sheet', 'filters' => $this->data]),
            fn () => (new BalanceSheetReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new BalanceSheetReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.balance_sheet', 'Balance Sheet');
    }
}

