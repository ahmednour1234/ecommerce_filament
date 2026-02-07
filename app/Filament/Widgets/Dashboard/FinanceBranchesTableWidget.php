<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Pages\Dashboard;
use App\Models\Finance\BranchTransaction;
use App\Services\Dashboard\DashboardService;
use App\Support\Money;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

class FinanceBranchesTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'ملخص المالية حسب الفروع';

    public function table(Table $table): Table
    {
        $filters = \App\Helpers\DashboardFilterHelper::parseFiltersFromRequest();
        $service = app(DashboardService::class);
        $branchesData = $service->getBranchFinancialSummary($filters);

        if (empty($branchesData)) {
            return $table
                ->query(fn () => BranchTransaction::query()->whereRaw('1 = 0'))
                ->emptyStateHeading('لا توجد بيانات')
                ->emptyStateDescription('لا توجد بيانات مالية في الفترة المحددة')
                ->columns([
                    TextColumn::make('branch_name')->label('بيان'),
                    TextColumn::make('income')->label('الإيراد'),
                    TextColumn::make('expense')->label('المصروف'),
                    TextColumn::make('net')->label('الصافي'),
                ])
                ->paginated(false);
        }

        $unionQueries = [];
        foreach ($branchesData as $row) {
            $unionQueries[] = DB::query()->selectRaw('? as id, ? as branch_name, ? as income, ? as expense, ? as net', [
                $row['id'],
                $row['branch_name'],
                $row['income'],
                $row['expense'],
                $row['net'],
            ]);
        }

        $unionQuery = null;
        foreach ($unionQueries as $uq) {
            $unionQuery = $unionQuery ? $unionQuery->unionAll($uq) : $uq;
        }

        $defaultCurrencyId = Money::defaultCurrencyId();

        return $table
            ->query(fn () => BranchTransaction::query()
                ->fromSub($unionQuery, 'branches_finance_data')
                ->select('branches_finance_data.*')
            )
            ->columns([
                TextColumn::make('branch_name')
                    ->label('بيان')
                    ->searchable()
                    ->weight(fn ($record) => str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz') ? 'bold' : 'normal')
                    ->color(fn ($record) => str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz') ? 'danger' : null),
                TextColumn::make('income')
                    ->label('الإيراد')
                    ->numeric(decimalPlaces: 2)
                    ->formatStateUsing(fn ($state) => Money::format($state, $defaultCurrencyId))
                    ->alignEnd()
                    ->weight(fn ($record) => str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz') ? 'bold' : 'normal')
                    ->color(fn ($record) => str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz') ? 'danger' : null),
                TextColumn::make('expense')
                    ->label('المصروف')
                    ->numeric(decimalPlaces: 2)
                    ->formatStateUsing(fn ($state) => Money::format($state, $defaultCurrencyId))
                    ->alignEnd()
                    ->weight(fn ($record) => str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz') ? 'bold' : 'normal')
                    ->color(fn ($record) => str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz') ? 'danger' : null),
                TextColumn::make('net')
                    ->label('الصافي')
                    ->numeric(decimalPlaces: 2)
                    ->formatStateUsing(fn ($state) => Money::format($state, $defaultCurrencyId))
                    ->alignEnd()
                    ->color(function ($record) {
                        if (str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz')) {
                            return 'danger';
                        }
                        return $record->net >= 0 ? 'success' : 'danger';
                    })
                    ->weight(fn ($record) => str_contains($record->id ?? '', 'total') || str_contains($record->id ?? '', 'zzz') ? 'bold' : 'normal'),
            ])
            ->defaultSort('id')
            ->paginated(false);
    }

    public function getTableRecordKey($record): string
    {
        return (string) ($record->id ?? uniqid());
    }
}
