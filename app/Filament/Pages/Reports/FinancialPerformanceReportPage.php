<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\FinancialPerformanceReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class FinancialPerformanceReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 15;
    protected static string $view = 'filament.pages.reports.financial-performance-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfMonth()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                ReportFilters::section([
                    'requireDateRange' => true,
                    'showAccount' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO($this->data);
        $service = new FinancialPerformanceReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('accounts')
                ->whereRaw('1 = 0')
                ->selectRaw('? as kpi, ? as value, ? as percentage', [
                    $row['kpi'] ?? '',
                    $row['value'] ?? 0,
                    $row['percentage'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('accounts')->whereRaw('1 = 0')
                ->selectRaw('NULL as kpi, 0 as value, 0 as percentage');
        }

        $query = \App\Models\Accounting\Account::query()
            ->fromSub($unionQuery, 'financial_performance_data')
            ->select('financial_performance_data.*');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('kpi')->label('KPI'),
                Tables\Columns\TextColumn::make('value')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('percentage')->formatStateUsing(fn ($state) => number_format($state, 2) . '%'),
            ])
            ->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'financial-performance', 'filters' => $this->data]),
            fn () => (new FinancialPerformanceReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new FinancialPerformanceReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return trans_dash('reports.financial_performance.navigation', 'Financial Performance');
    }
}

