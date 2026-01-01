<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\JournalEntriesByYearReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class JournalEntriesByYearReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 9;
    protected static string $view = 'filament.pages.reports.journal-entries-by-year-report';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfYear()->format('Y-m-d'),
            'to_date' => now()->format('Y-m-d'),
            'posted_only' => true,
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
        $service = new JournalEntriesByYearReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('journal_entries')
                ->whereRaw('1 = 0')
                ->selectRaw('? as year, ? as month, ? as month_name, ? as entry_count, ? as total_debit, ? as total_credit', [
                    $row['year'] ?? '',
                    $row['month'] ?? '',
                    $row['month_name'] ?? '',
                    $row['entry_count'] ?? 0,
                    $row['total_debit'] ?? 0,
                    $row['total_credit'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('journal_entries')->whereRaw('1 = 0')
                ->selectRaw('NULL as year, NULL as month, NULL as month_name, 0 as entry_count, 0 as total_debit, 0 as total_credit');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\JournalEntry::query()
                ->fromSub($unionQuery, 'journal_entries_by_year_data')
                ->select('journal_entries_by_year_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('month_name')->label('Month'),
                Tables\Columns\TextColumn::make('entry_count')->label('Entry Count'),
                Tables\Columns\TextColumn::make('total_debit')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('total_credit')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->defaultSort('year', 'desc')
            ->defaultSort('month', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'journal-entries-by-year', 'filters' => $this->data]),
            fn () => (new JournalEntriesByYearReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new JournalEntriesByYearReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.journal_entries_by_year', 'Journal Entries by Year');
    }
}

