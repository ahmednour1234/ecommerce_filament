<?php

namespace App\Filament\Widgets\Dashboard;

use App\Helpers\DashboardFilterHelper;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redirect;

class DashboardFilterWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.dashboard-filter-widget';
    protected int|string|array $columnSpan = 'full';

    public ?string $date_from = null;
    public ?string $date_to = null;

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
            ->schema([
                Grid::make(2)
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('Start date')
                            ->required()
                            ->default(fn () => $this->date_from ?? now()->startOfMonth()->format('Y-m-d'))
                            ->displayFormat('d/m/Y')
                            ->native(false),

                        DatePicker::make('date_to')
                            ->label('End date')
                            ->required()
                            ->default(fn () => $this->date_to ?? now()->endOfMonth()->format('Y-m-d'))
                            ->displayFormat('d/m/Y')
                            ->native(false),
                    ]),
            ]);
    }

    public function applyFilters(): void
    {
        $this->validate();

        $data = $this->filterForm->getState();

        $filters = [
            'date_from' => !empty($data['date_from']) ? Carbon::parse($data['date_from'])->startOfDay() : null,
            'date_to' => !empty($data['date_to']) ? Carbon::parse($data['date_to'])->endOfDay() : null,
        ];

        $filters = DashboardFilterHelper::validateDateRange($filters);
        $queryString = DashboardFilterHelper::buildFilterQueryString($filters);

        $url = request()->url() . ($queryString ? '?' . $queryString : '');
        
        return redirect($url);
    }
}
