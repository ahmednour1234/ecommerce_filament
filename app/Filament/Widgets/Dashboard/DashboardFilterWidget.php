<?php

namespace App\Filament\Widgets\Dashboard;

use App\Helpers\DashboardFilterHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class DashboardFilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.dashboard-filter-widget';
    protected int|string|array $columnSpan = 'full';

    public ?string $date_from = null;
    public ?string $date_to = null;

    protected $queryString = [
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
    ];

    public function mount(): void
    {
        $filters = DashboardFilterHelper::parseFiltersFromRequest();
        $filters = DashboardFilterHelper::validateDateRange($filters);

        $this->date_from = $filters['date_from']?->format('Y-m-d') ?? now()->startOfMonth()->format('Y-m-d');
        $this->date_to = $filters['date_to']?->format('Y-m-d') ?? now()->endOfMonth()->format('Y-m-d');

        $this->filterForm->fill([
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'filterForm',
        ];
    }

    public function filterForm(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->statePath('filterForm')
            ->schema([
                Grid::make(2)
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('Start date')
                            ->required()
                            ->default(fn () => $this->date_from ?? now()->startOfMonth()->format('Y-m-d'))
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->date_from = $state;
                            }),

                        DatePicker::make('date_to')
                            ->label('End date')
                            ->required()
                            ->default(fn () => $this->date_to ?? now()->endOfMonth()->format('Y-m-d'))
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->date_to = $state;
                            }),
                    ]),
            ]);
    }

    public function updatedDateFrom(): void
    {
        $this->dispatch('filters-updated');
    }

    public function updatedDateTo(): void
    {
        $this->dispatch('filters-updated');
    }

    public function applyFilters(): void
    {
        $this->validate();

        $data = $this->filterForm->getState();

        $this->date_from = $data['date_from'] ?? $this->date_from;
        $this->date_to = $data['date_to'] ?? $this->date_to;

        $this->dispatch('filters-updated');
    }
}
