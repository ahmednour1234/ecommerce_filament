<?php

namespace App\Filament\Resources\Finance\FinanceTypeResource\Pages;

use App\Filament\Resources\Finance\FinanceTypeResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListFinanceTypes extends ListRecords
{
    protected static string $resource = FinanceTypeResource::class;

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(function ($query) {
                return $query;
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Tables\Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.manage_types') ?? false),
        ];
    }
}
