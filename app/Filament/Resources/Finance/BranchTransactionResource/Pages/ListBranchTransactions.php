<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ListBranchTransactions extends ListRecords
{
    protected static string $resource = BranchTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Tables\Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.create_transactions') ?? false),
        ];
    }
}
