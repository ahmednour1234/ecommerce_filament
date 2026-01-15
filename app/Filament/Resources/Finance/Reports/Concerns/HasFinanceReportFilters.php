<?php

namespace App\Filament\Resources\Finance\Reports\Concerns;

use App\Models\MainCore\Branch;
use App\Models\MainCore\Country;
use App\Models\MainCore\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Carbon;

trait HasFinanceReportFilters
{
    public ?string $from = null;
    public ?string $to = null;

    public ?int $branch_id = null;
    public ?int $country_id = null;
    public ?int $currency_id = null;

    public ?string $status = null;   // pending|approved|rejected|null
    public ?string $group_by = 'day'; // day|month

    public function initDefaultDates(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->to   = now()->toDateString();
    }

    public function filtersForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('from')
                ->label(tr('reports.filters.from', [], null, 'dashboard'))
                ->live(),

            Forms\Components\DatePicker::make('to')
                ->label(tr('reports.filters.to', [], null, 'dashboard'))
                ->live(),

            Forms\Components\Select::make('branch_id')
                ->label(tr('reports.filters.branch', [], null, 'dashboard'))
                ->options(fn () => Branch::where('status', 'active')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->live(),

            Forms\Components\Select::make('country_id')
                ->label(tr('reports.filters.country', [], null, 'dashboard'))
                ->options(fn () => Country::pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->live(),

            Forms\Components\Select::make('currency_id')
                ->label(tr('reports.filters.currency', [], null, 'dashboard'))
                ->options(fn () => Currency::where('is_active', true)->pluck('code', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->live(),

            Forms\Components\Select::make('status')
                ->label(tr('reports.filters.status', [], null, 'dashboard'))
                ->options([
                    'pending'  => tr('tables.branch_tx.status_pending', [], null, 'dashboard'),
                    'approved' => tr('tables.branch_tx.status_approved', [], null, 'dashboard'),
                    'rejected' => tr('tables.branch_tx.status_rejected', [], null, 'dashboard'),
                ])
                ->nullable()
                ->live(),

            Forms\Components\Select::make('group_by')
                ->label(tr('reports.filters.group_by', [], null, 'dashboard'))
                ->options([
                    'day'   => tr('reports.filters.group_by_day', [], null, 'dashboard'),
                    'month' => tr('reports.filters.group_by_month', [], null, 'dashboard'),
                ])
                ->live(),
        ])->columns(7);
    }

    public function dateRange(): array
    {
        $from = $this->from
            ? Carbon::parse($this->from)->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = $this->to
            ? Carbon::parse($this->to)->endOfDay()
            : now()->endOfDay();

        return [$from, $to];
    }
}
