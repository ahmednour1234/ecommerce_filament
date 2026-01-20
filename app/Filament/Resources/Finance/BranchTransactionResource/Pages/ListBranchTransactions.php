<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchTransactions extends ListRecords
{
    protected static string $resource = BranchTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin') || auth()->user()?->can('finance.create_transactions') ?? false),
        ];
    }
}
