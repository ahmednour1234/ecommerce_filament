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

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('customers')
                ->whereRaw('1 = 0')
                ->selectRaw('? as customer_code, ? as customer_name, ? as balance, ? as credit_limit', [
                    $row['customer_code'] ?? '',
                    $row['customer_name'] ?? '',
                    $row['balance'] ?? 0,
                    $row['credit_limit'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('customers')->whereRaw('1 = 0')
                ->selectRaw('NULL as customer_code, NULL as customer_name, 0 as balance, 0 as credit_limit');
        }

        $query = \App\Models\Sales\Customer::query()
            ->fromSub($unionQuery, 'ar_report_data')
            ->select('ar_report_data.*');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('customer_code'),
                Tables\Columns\TextColumn::make('customer_name'),
                Tables\Columns\TextColumn::make('balance')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('credit_limit')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
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

