<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\AccountsReceivableReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class AccountsReceivableReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.pages.reports.accounts-receivable-report';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.accounts_receivable.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.accounts_receivable.title', [], null, 'dashboard');
    }

    public function mount(): void
    {
        $this->form->fill([
            'to_date' => now()->format('Y-m-d'),
            'include_zero_rows' => false,
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
        $service = new AccountsReceivableReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];
        $index = 0;

        foreach ($rows as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as customer_code, ? as customer_name, ? as balance, ? as credit_limit', [
                $index++,
                $row['customer_code'] ?? '',
                $row['customer_name'] ?? '',
                $row['balance'] ?? 0,
                $row['credit_limit'] ?? 0,
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw('0 as id, NULL as customer_code, NULL as customer_name, 0 as balance, 0 as credit_limit');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Sales\Customer::query()
                ->fromSub($unionQuery, 'ar_report_data')
                ->select('ar_report_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer_code')
                    ->label(trans_dash('reports.accounts_receivable.customer_code', 'Customer Code')),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label(trans_dash('reports.accounts_receivable.customer_name', 'Customer Name')),
                Tables\Columns\TextColumn::make('balance')
                    ->label(trans_dash('reports.accounts_receivable.balance', 'Balance'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('credit_limit')
                    ->label(trans_dash('reports.accounts_receivable.credit_limit', 'Credit Limit'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->emptyStateHeading(tr('pages.reports.accounts_receivable.empty_state', [], null, 'dashboard'))
            ->defaultSort('balance', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'accounts-receivable', 'filters' => $this->data]),
            fn () => (new AccountsReceivableReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new AccountsReceivableReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.accounts_receivable', 'Accounts Receivable');
    }
}

