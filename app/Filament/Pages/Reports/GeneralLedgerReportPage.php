<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\GeneralLedgerReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

/**
 * General Ledger Report Page
 */
class GeneralLedgerReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.reports.general-ledger-report';

    public ?array $data = [];
    protected ?GeneralLedgerReportService $reportService = null;

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.general_ledger.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.general_ledger.title', [], null, 'dashboard');
    }

    public function mount(): void
    {
        $this->form->fill([
            'from_date' => now()->startOfMonth()->format('Y-m-d'),
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
                    'showAccount' => true,
                    'showCurrency' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO($this->data);
        $this->reportService = new GeneralLedgerReportService($filters);
        $reportData = $this->reportService->getData();

        // Build query for table
        $rows = $reportData->rows;
        $unionQueries = [];
        $index = 0;

        foreach ($rows as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as date, ? as entry_number, ? as reference, ? as description, ? as debit, ? as credit, ? as balance, ? as branch, ? as cost_center', [
                $index++,
                $row['date'] ?? '',
                $row['entry_number'] ?? '',
                $row['reference'] ?? '',
                $row['description'] ?? '',
                $row['debit'] ?? 0,
                $row['credit'] ?? 0,
                $row['balance'] ?? 0,
                $row['branch'] ?? '',
                $row['cost_center'] ?? '',
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            if ($unionQuery === null) {
                $unionQuery = $uq;
            } else {
                $unionQuery->unionAll($uq);
            }
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw('0 as id, NULL as date, NULL as entry_number, NULL as reference, NULL as description, 0 as debit, 0 as credit, 0 as balance, NULL as branch, NULL as cost_center');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\GeneralLedgerEntry::query()
                ->fromSub($unionQuery, 'general_ledger_data')
                ->select('general_ledger_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(trans_dash('reports.general_ledger.date', 'Date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('entry_number')
                    ->label(trans_dash('reports.general_ledger.entry_number', 'Entry #'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference')
                    ->label(trans_dash('reports.general_ledger.reference', 'Reference'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $ref = $record->reference ?? '';
                        if ($ref === 'TOTAL') {
                            return '<strong>' . $ref . '</strong>';
                        }
                        return $ref;
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('description')
                    ->label(trans_dash('reports.general_ledger.description', 'Description'))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description ?? ''),

                Tables\Columns\TextColumn::make('debit')
                    ->label(trans_dash('reports.general_ledger.debit', 'Debit'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(\App\Support\Money::defaultCurrencyCode()),
                    ]),

                Tables\Columns\TextColumn::make('credit')
                    ->label(trans_dash('reports.general_ledger.credit', 'Credit'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money(\App\Support\Money::defaultCurrencyCode()),
                    ]),

                Tables\Columns\TextColumn::make('balance')
                    ->label(trans_dash('reports.general_ledger.balance', 'Balance'))
                    ->money(\App\Support\Money::defaultCurrencyCode())
                    ->sortable()
                    ->color(fn ($record) => ($record->balance ?? 0) < 0 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('branch')
                    ->label(trans_dash('reports.general_ledger.branch', 'Branch'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cost_center')
                    ->label(trans_dash('reports.general_ledger.cost_center', 'Cost Center'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('date', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'general-ledger', 'filters' => $this->data]),
            fn () => $this->exportPdf(),
            fn () => $this->exportExcel()
        );
    }

    protected function exportPdf()
    {
        $filters = new FilterDTO($this->data);
        $service = new GeneralLedgerReportService($filters);
        return $service->exportPdf();
    }

    protected function exportExcel()
    {
        $filters = new FilterDTO($this->data);
        $service = new GeneralLedgerReportService($filters);
        return $service->exportExcel();
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.general_ledger', 'General Ledger');
    }
}

