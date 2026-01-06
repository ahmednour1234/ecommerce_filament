<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Actions\ReportExportActions;
use App\Filament\Forms\Components\ReportFilters;
use App\Reports\DTOs\FilterDTO;
use App\Services\Reports\FixedAssetsReportService;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class FixedAssetsReportPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 8;
    protected static string $view = 'filament.pages.reports.fixed-assets-report';

    public ?array $data = [];

    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }

    public function getTitle(): string
    {
        return tr('pages.reports.fixed_assets.title', [], null, 'dashboard');
    }

    public function getHeading(): string
    {
        return tr('pages.reports.fixed_assets.title', [], null, 'dashboard');
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
        $service = new FixedAssetsReportService($filters);
        $reportData = $service->getData();

        $rows = $reportData->rows;
        $unionQueries = [];
        $index = 0;

        foreach ($rows as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as code, ? as name, ? as category, ? as purchase_date, ? as acquisition_cost, ? as depreciation, ? as net_book_value, ? as status', [
                $index++,
                $row['code'] ?? '',
                $row['name'] ?? '',
                $row['category'] ?? '',
                $row['purchase_date'] ?? '',
                $row['acquisition_cost'] ?? 0,
                $row['depreciation'] ?? 0,
                $row['net_book_value'] ?? 0,
                $row['status'] ?? '',
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        if ($unionQuery === null) {
            $unionQuery = DB::query()->selectRaw('0 as id, NULL as code, NULL as name, NULL as category, NULL as purchase_date, 0 as acquisition_cost, 0 as depreciation, 0 as net_book_value, NULL as status');
        }

        // Filament Tables requires an Eloquent Builder, not a Query Builder.
        // Wrap in closure to ensure Filament receives a proper Eloquent Builder instance.
        return $table
            ->query(fn () => \App\Models\Accounting\Asset::query()
                ->fromSub($unionQuery, 'fixed_assets_data')
                ->select('fixed_assets_data.*')
            )
            ->columns([
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('purchase_date')->date(),
                Tables\Columns\TextColumn::make('acquisition_cost')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('depreciation')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('net_book_value')->money(\App\Support\Money::defaultCurrencyCode()),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->emptyStateHeading(tr('pages.reports.fixed_assets.empty_state', [], null, 'dashboard'))
            ->defaultSort('code', 'asc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getHeaderActions(): array
    {
        return ReportExportActions::actions(
            fn () => route('reports.print', ['report' => 'fixed-assets', 'filters' => $this->data]),
            fn () => (new FixedAssetsReportService(new FilterDTO($this->data)))->exportPdf(),
            fn () => (new FixedAssetsReportService(new FilterDTO($this->data)))->exportExcel()
        );
    }

    public static function getNavigationLabel(): string
    {
        return tr('menu.reports.fixed_assets', 'Fixed Assets Report');
    }
}

