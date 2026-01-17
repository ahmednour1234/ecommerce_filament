<?php

namespace App\Filament\Resources\HR\SalaryComponentResource\Pages;

use App\Filament\Resources\HR\SalaryComponentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSalaryComponents extends ListRecords
{
    protected static string $resource = SalaryComponentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->can('hr_components.create') ?? false),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->badge(fn () => \App\Models\HR\SalaryComponent::count()),
            'earnings' => Tab::make(trans_dash('tables.salary_components.earnings') ?: 'Earnings')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'earning'))
                ->badge(fn () => \App\Models\HR\SalaryComponent::where('type', 'earning')->count()),
            'deductions' => Tab::make(trans_dash('tables.salary_components.deductions') ?: 'Deductions')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'deduction'))
                ->badge(fn () => \App\Models\HR\SalaryComponent::where('type', 'deduction')->count()),
        ];
    }
}
