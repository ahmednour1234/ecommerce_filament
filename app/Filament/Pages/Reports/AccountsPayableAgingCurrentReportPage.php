<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
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
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 11;
    protected static string $view = 'filament.pages.reports.accounts-payable-aging-current-report';

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
        $service = new AccountsPayableAgingCurrentReportService($filters);
        $reportData = $service->getData();

        // Simplified table - will be enhanced when AP tables are available
        return $table
            ->query(\App\Models\Accounting\Account::query()->whereRaw('1 = 0'))
            ->columns([
                Tables\Columns\TextColumn::make('supplier')->label('Supplier'),
                Tables\Columns\TextColumn::make('current')->label('0-30 Days')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('days_31_60')->label('31-60 Days')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('days_61_90')->label('61-90 Days')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('over_90')->label('Over 90 Days')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('total')->label('Total')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
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
        return trans_dash('reports.accounts_payable_aging_current.navigation', 'A/P Aging Current');
    }
}

