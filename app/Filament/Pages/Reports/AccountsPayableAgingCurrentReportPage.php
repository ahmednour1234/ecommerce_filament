<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Filament\Concerns\AccountingModuleGate;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\AccountsPayableAgingCurrentReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;

class AccountsPayableAgingCurrentReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms, AccountingModuleGate;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 11;
    protected static string $view = 'filament.pages.reports.accounts-payable-aging-current-report';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.accounts_payable_aging_current.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.accounts_payable_aging_current.title', [], null, 'dashboard');
    }

    public function mount(): void
    {
        $this->form->fill([]);
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
        $service = new AccountsPayableAgingCurrentReportService($filters);
        $reportData = $service->getData();

        // Simplified table - will be enhanced when AP tables are available
        return $table
            ->query(\App\Models\Accounting\Account::query()->whereRaw('1 = 0'))
            ->columns([
                Tables\Columns\TextColumn::make('supplier')
                    ->label(trans_dash('reports.accounts_payable_aging_current.supplier', 'Supplier')),
                Tables\Columns\TextColumn::make('current')
                    ->label(trans_dash('reports.accounts_payable_aging_current.current', '0-30 Days'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('days_31_60')
                    ->label(trans_dash('reports.accounts_payable_aging_current.days_31_60', '31-60 Days'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('days_61_90')
                    ->label(trans_dash('reports.accounts_payable_aging_current.days_61_90', '61-90 Days'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('over_90')
                    ->label(trans_dash('reports.accounts_payable_aging_current.over_90', 'Over 90 Days'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('total')
                    ->label(trans_dash('reports.accounts_payable_aging_current.total', 'Total'))
                    ->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->emptyStateHeading(tr('pages.reports.accounts_payable_aging_current.empty_state', [], null, 'dashboard'))
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'accounts-payable-aging-current', 'filters' => $this->data]),
            fn () => (new AccountsPayableAgingCurrentReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new AccountsPayableAgingCurrentReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.accounts_payable_aging', 'Accounts Payable Aging');
    }
}

