<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\AccountsPayableAgingOverdueReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;

class AccountsPayableAgingOverdueReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 12;
    protected static string $view = 'filament.pages.reports.accounts-payable-aging-overdue-report';

    public ?array $data = [];

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
        $service = new AccountsPayableAgingOverdueReportService($filters);
        $reportData = $service->getData();

        return $table
            ->query(\App\Models\Accounting\Account::query()->whereRaw('1 = 0'))
            ->columns([
                Tables\Columns\TextColumn::make('supplier')->label('Supplier'),
                Tables\Columns\TextColumn::make('overdue_amount')->label('Overdue Amount')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('days_overdue')->label('Days Overdue'),
            ])
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'accounts-payable-aging-overdue', 'filters' => $this->data]),
            fn () => (new AccountsPayableAgingOverdueReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new AccountsPayableAgingOverdueReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return trans_dash('reports.accounts_payable_aging_overdue.navigation', 'A/P Aging Overdue');
    }
}

