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

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.comparisons.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.comparisons.title', [], null, 'dashboard');
    }

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
                \Filament\Forms\Components\Section::make(tr('pages.reports.comparisons.period_b', [], null, 'dashboard'))
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('period_b_from')
                            ->label(tr('pages.reports.comparisons.period_b_from', [], null, 'dashboard'))
                            ->required(),
                        \Filament\Forms\Components\DatePicker::make('period_b_to')
                            ->label(tr('pages.reports.comparisons.period_b_to', [], null, 'dashboard'))
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $periodAFrom = $this->data['from_date'] ?? null;
        $periodBFrom = $this->data['period_b_from'] ?? null;

        // Validate both periods are set before calling service
        if (!$periodAFrom || !$periodBFrom) {
            return $table
                ->query(fn () => \App\Models\Accounting\Account::query()->whereRaw('1 = 0'))
                ->columns([
                    Tables\Columns\TextColumn::make('period'),
                    Tables\Columns\TextColumn::make('from_date'),
                    Tables\Columns\TextColumn::make('to_date'),
                    Tables\Columns\TextColumn::make('amount')->money(\App\Support\Money::defaultCurrencyCode()),
                ])
                ->emptyStateHeading(tr('pages.reports.comparisons.error_both_periods_required', [], null, 'dashboard'))
                ->paginated(false);
        }

        $filters = new FilterDTO(array_merge($this->data, [
            'period_a_from' => $periodAFrom,
            'period_a_to' => $this->data['to_date'] ?? null,
            'period_b_from' => $periodBFrom,
            'period_b_to' => $this->data['period_b_to'] ?? null,
        ]));
        
        try {
            $service = new ComparisonsReportService($filters);
            $reportData = $service->getData();
        } catch (\InvalidArgumentException $e) {
            return $table
                ->query(fn () => \App\Models\Accounting\Account::query()->whereRaw('1 = 0'))
                ->columns([
                    Tables\Columns\TextColumn::make('period'),
                    Tables\Columns\TextColumn::make('from_date'),
                    Tables\Columns\TextColumn::make('to_date'),
                    Tables\Columns\TextColumn::make('amount')->money(\App\Support\Money::defaultCurrencyCode()),
                ])
                ->emptyStateHeading($e->getMessage())
                ->paginated(false);
        }

        $rows = $reportData->rows;
        $unionQueries = [];

        $index = 0;
        foreach ($rows as $row) {
            $unionQueries[] = DB::table('accounts')
                ->whereRaw('1 = 0')
                ->selectRaw('? as id, ? as period, ? as from_date, ? as to_date, ? as amount', [
                    $index++,
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
                ->selectRaw('0 as id, NULL as period, NULL as from_date, NULL as to_date, 0 as amount');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\Account::query()
                ->fromSub($unionQuery, 'comparisons_data')
                ->select('comparisons_data.*')
                ->withoutGlobalScopes() // Remove any global scopes that might add ordering
            )
            ->columns([
                Tables\Columns\TextColumn::make('period'),
                Tables\Columns\TextColumn::make('from_date'),
                Tables\Columns\TextColumn::make('to_date'),
                Tables\Columns\TextColumn::make('amount')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->defaultSort('id', 'asc')
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

