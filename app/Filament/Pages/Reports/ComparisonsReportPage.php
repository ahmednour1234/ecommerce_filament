<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\ComparisonsReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class ComparisonsReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 16;
    protected static string $view = 'filament.pages.reports.comparisons-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->subYear()->startOfMonth()->format('Y-m-d'),
            'to_date' => now()->subYear()->endOfMonth()->format('Y-m-d'),
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
                \Filament\Forms\Components\Section::make('Period B')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('period_b_from')
                            ->label('Period B From Date')
                            ->required(),
                        \Filament\Forms\Components\DatePicker::make('period_b_to')
                            ->label('Period B To Date')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO(array_merge($this->data, [
            'period_a_from' => $this->data['from_date'] ?? null,
            'period_a_to' => $this->data['to_date'] ?? null,
            'period_b_from' => $this->data['period_b_from'] ?? null,
            'period_b_to' => $this->data['period_b_to'] ?? null,
        ]));
        $service = new ComparisonsReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('accounts')
                ->whereRaw('1 = 0')
                ->selectRaw('? as period, ? as from_date, ? as to_date, ? as amount', [
                    $row['period'] ?? '',
                    $row['from_date'] ?? '',
                    $row['to_date'] ?? '',
                    $row['amount'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('accounts')->whereRaw('1 = 0')
                ->selectRaw('NULL as period, NULL as from_date, NULL as to_date, 0 as amount');
        }

        $query = \App\Models\Accounting\Account::query()
            ->fromSub($unionQuery, 'comparisons_data')
            ->select('comparisons_data.*');

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('period'),
                Tables\Columns\TextColumn::make('from_date'),
                Tables\Columns\TextColumn::make('to_date'),
                Tables\Columns\TextColumn::make('amount')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->paginated(false);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'comparisons', 'filters' => $this->data]),
            fn () => (new ComparisonsReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new ComparisonsReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.comparisons_report', 'Comparisons Report');
    }
}

