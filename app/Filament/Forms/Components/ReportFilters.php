<?php

namespace App\Filament\Forms\Components;

use App\Models\Accounting\Account;
use App\Models\MainCore\Branch;
use App\Models\MainCore\CostCenter;
use App\Models\MainCore\Currency;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

/**
 * Unified Report Filters Component
 * Reusable filter form component for all reports
 */
class ReportFilters
{
    /**
     * Get standard filter schema
     *
     * @param array $options Additional options:
     *   - requireDateRange: bool (default: true)
     *   - showAccount: bool (default: true)
     *   - showCurrency: bool (default: true)
     *   - showProject: bool (default: false)
     *   - showFiscalYear: bool (default: false)
     *   - showPeriod: bool (default: false)
     *   - dateLabel: string (default: 'Date Range')
     *   - columns: int (default: 3)
     * @return array
     */
    public static function schema(array $options = []): array
    {
        $requireDateRange = $options['requireDateRange'] ?? true;
        $showAccount = $options['showAccount'] ?? true;
        $requireAccount = $options['requireAccount'] ?? false;
        $showCurrency = $options['showCurrency'] ?? true;
        $showProject = $options['showProject'] ?? false;
        $showFiscalYear = $options['showFiscalYear'] ?? false;
        $showPeriod = $options['showPeriod'] ?? false;
        $dateLabel = $options['dateLabel'] ?? 'Date Range';
        $columns = $options['columns'] ?? 3;

        $schema = [];

        // Date Range
        if ($requireDateRange) {
            $schema[] = DatePicker::make('from_date')
                ->label(trans_dash('reports.filters.from_date', 'From Date'))
                ->required($requireDateRange)
                ->default(now()->startOfMonth())
                ->reactive();

            $schema[] = DatePicker::make('to_date')
                ->label(trans_dash('reports.filters.to_date', 'To Date'))
                ->required($requireDateRange)
                ->default(now())
                ->reactive();
        }

        // Branch
        $schema[] = Select::make('branch_id')
            ->label(trans_dash('reports.filters.branch', 'Branch'))
            ->options(fn () => Branch::active()->pluck('name', 'id'))
            ->searchable()
            ->preload()
            ->nullable()
            ->reactive()
            ->afterStateUpdated(fn ($component) => $component->getLivewire()?->dispatch('filters-updated'));

        // Cost Center
        $schema[] = Select::make('cost_center_id')
            ->label(trans_dash('reports.filters.cost_center', 'Cost Center'))
            ->options(fn () => CostCenter::active()->pluck('name', 'id'))
            ->searchable()
            ->preload()
            ->nullable()
            ->reactive()
            ->afterStateUpdated(fn ($component) => $component->getLivewire()?->dispatch('filters-updated'));

        // Account (if enabled)
        if ($showAccount) {
            $accountField = Select::make('account_id')
                ->label(trans_dash('reports.filters.account', 'Account'))
                ->options(fn () => Account::active()->orderBy('code')->get()->mapWithKeys(function ($account) {
                    return [$account->id => $account->code . ' - ' . $account->name];
                }))
                ->searchable()
                ->preload()
                ->reactive();
            
            if ($requireAccount) {
                $accountField->required();
            } else {
                $accountField->nullable();
            }
            
            $schema[] = $accountField;
        }

        // Currency (if enabled)
        if ($showCurrency) {
            $schema[] = Select::make('currency_id')
                ->label(trans_dash('reports.filters.currency', 'Currency'))
                ->options(fn () => Currency::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive();
        }

        // Project (if enabled)
        if ($showProject) {
            $schema[] = Select::make('project_id')
                ->label(trans_dash('reports.filters.project', 'Project'))
                ->options(fn () => \App\Models\Accounting\Project::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive();
        }

        // Fiscal Year (if enabled)
        if ($showFiscalYear) {
            $schema[] = Select::make('fiscal_year_id')
                ->label(trans_dash('reports.filters.fiscal_year', 'Fiscal Year'))
                ->options(fn () => \App\Models\Accounting\FiscalYear::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive();
        }

        // Period (if enabled)
        if ($showPeriod) {
            $schema[] = Select::make('period_id')
                ->label(trans_dash('reports.filters.period', 'Period'))
                ->options(fn () => \App\Models\Accounting\Period::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->reactive();
        }

        // Toggles
        $schema[] = Toggle::make('include_zero_rows')
            ->label(trans_dash('reports.filters.include_zero_rows', 'Include Zero Rows'))
            ->default(false)
            ->reactive()
            ->afterStateUpdated(fn ($component) => $component->getLivewire()?->dispatch('filters-updated'));

        $schema[] = Toggle::make('posted_only')
            ->label(trans_dash('reports.filters.posted_only', 'Posted Only'))
            ->default(true)
            ->reactive()
            ->afterStateUpdated(fn ($component) => $component->getLivewire()?->dispatch('filters-updated'));

        return $schema;
    }

    /**
     * Get filter schema wrapped in a Section
     *
     * @param array $options
     * @return \Filament\Forms\Components\Section
     */
    public static function section(array $options = []): \Filament\Forms\Components\Section
    {
        $label = $options['label'] ?? trans_dash('reports.filters.title', 'Report Filters');
        $columns = $options['columns'] ?? 3;

        return \Filament\Forms\Components\Section::make($label)
            ->schema(self::schema($options))
            ->columns($columns)
            ->collapsible()
            ->collapsed(false);
    }
}

