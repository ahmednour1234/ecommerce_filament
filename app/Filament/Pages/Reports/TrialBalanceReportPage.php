<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\TrialBalanceReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

/**
 * Trial Balance Report Page
 */
class TrialBalanceReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.reports.trial-balance-report';

    public ?array $data = [];
    protected ?TrialBalanceReportService $reportService = null;

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
        $this->reportService = new TrialBalanceReportService($filters);
        $reportData = $this->reportService->getData();

        // Build query for table
        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('accounts')
                ->whereRaw('1 = 0')
                ->selectRaw('? as account_code, ? as account_name, ? as account_type, ? as debits, ? as credits, ? as balance', [
                    $row['account_code'] ?? '',
                    $row['account_name'] ?? '',
                    $row['account_type'] ?? '',
                    $row['debits'] ?? 0,
                    $row['credits'] ?? 0,
                    $row['balance'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            if ($unionQuery === null) {
                $unionQuery = $uq;
            } else {
                $unionQuery->union($uq);
            }
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('accounts')->whereRaw('1 = 0')
                ->selectRaw('NULL as account_code, NULL as account_name, NULL as account_type, 0 as debits, 0 as credits, 0 as balance');
        }

        $query = \App\Models\Accounting\Account::query()
            ->fromSub($unionQuery, 'trial_balance_data')
            ->select('trial_balance_data.*');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('account_code')
                    ->label(trans_dash('reports.trial_balance.account_code', 'Account Code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('account_name')
                    ->label(trans_dash('reports.trial_balance.account_name', 'Account Name'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $name = $record->account_name ?? '';
                        if ($name === 'TOTAL') {
                            return '<strong>' . $name . '</strong>';
                        }
                        return $name;
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('account_type')
                    ->label(trans_dash('reports.trial_balance.account_type', 'Type'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('debits')
                    ->label(trans_dash('reports.trial_balance.debits', 'Debits'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(\App\Support\Money::defaultCurrencyCode()),
                    ]),

                Tables\Columns\TextColumn::make('credits')
                    ->label(trans_dash('reports.trial_balance.credits', 'Credits'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(\App\Support\Money::defaultCurrencyCode()),
                    ]),

                Tables\Columns\TextColumn::make('balance')
                    ->label(trans_dash('reports.trial_balance.balance', 'Balance'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable()
                    ->color(fn ($record) => ($record->balance ?? 0) < 0 ? 'danger' : 'success'),
            ])
            ->defaultSort('account_code', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'trial-balance', 'filters' => $this->data]),
            fn () => $this->exportPdf(),
            fn () => $this->exportExcel()
        );
    }

    protected function exportPdf()
    {
        $filters = new FilterDTO($this->data);
        $service = new TrialBalanceReportService($filters);
        return $service->exportPdf();
    }

    protected function exportExcel()
    {
        $filters = new FilterDTO($this->data);
        $service = new TrialBalanceReportService($filters);
        return $service->exportExcel();
    }

    public static function getNavigationLabel(): string
    {
        return trans_dash('reports.trial_balance.navigation', 'Trial Balance');
    }
}

