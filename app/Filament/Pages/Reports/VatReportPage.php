<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\VatReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class VatReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 7;
    protected static string $view = 'filament.pages.reports.vat-report';

    public ?array $data = [];

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
                    'showAccount' => false,
                ]),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        $filters = new FilterDTO($this->data);
        $service = new VatReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];

        foreach ($rows as $row) {
            $unionQueries[] = DB::table('journal_entry_lines')
                ->whereRaw('1 = 0')
                ->selectRaw('? as date, ? as account_code, ? as account_name, ? as entry_number, ? as output_vat, ? as input_vat', [
                    $row['date'] ?? '',
                    $row['account_code'] ?? '',
                    $row['account_name'] ?? '',
                    $row['entry_number'] ?? '',
                    $row['output_vat'] ?? 0,
                    $row['input_vat'] ?? 0,
                ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->union($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::table('journal_entry_lines')->whereRaw('1 = 0')
                ->selectRaw('NULL as date, NULL as account_code, NULL as account_name, NULL as entry_number, 0 as output_vat, 0 as input_vat');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\JournalEntryLine::query()
                ->fromSub($unionQuery, 'vat_report_data')
                ->select('vat_report_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('date')->date(),
                Tables\Columns\TextColumn::make('account_code'),
                Tables\Columns\TextColumn::make('account_name'),
                Tables\Columns\TextColumn::make('entry_number'),
                Tables\Columns\TextColumn::make('output_vat')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('input_vat')->money(\App\Support\Money::defaultCurrencyCode()),
            ])
            ->defaultSort('date', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'vat', 'filters' => $this->data]),
            fn () => (new VatReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new VatReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.vat_report', 'VAT Report');
    }
}

