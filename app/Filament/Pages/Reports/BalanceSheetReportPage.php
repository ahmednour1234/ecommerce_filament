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

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('accounts')
                ->whereRaw('1 = 0')
                ->selectRaw('? as section, ? as account_code, ? as account_name, ? as balance', [
                    $row['section'] ?? '',
                    $row['account_code'] ?? '',
                    $row['account_name'] ?? '',
                    $row['balance'] ?? null,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('accounts')->whereRaw('1 = 0')
                ->selectRaw('NULL as section, NULL as account_code, NULL as account_name, NULL as balance');
        }

        // Use a simple query builder to avoid model's default ordering
        $query = DB::table(DB::raw('(' . $unionQuery->toSql() . ') as balance_sheet_data'))
            ->mergeBindings($unionQuery);

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('account_code'),
                Tables\Columns\TextColumn::make('account_name')
                    ->formatStateUsing(fn ($record) => $record->account_name === 'TOTAL' ? '<strong>' . $record->account_name . '</strong>' : $record->account_name)
                    ->html(),
                Tables\Columns\TextColumn::make('balance')
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

