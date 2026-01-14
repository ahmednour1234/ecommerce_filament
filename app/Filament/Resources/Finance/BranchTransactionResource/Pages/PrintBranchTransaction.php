<?php

namespace App\Filament\Resources\Finance\BranchTransactionResource\Pages;

use App\Filament\Resources\Finance\BranchTransactionResource;
use App\Models\Finance\BranchTransaction;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class PrintBranchTransaction extends Page
{
    use InteractsWithRecord;

    protected static string $resource = BranchTransactionResource::class;

    // ✅ لازم الـ view ده يكون موجود فعلاً
    protected static string $view = 'filament.finance.branch-transactions.print';

    public function mount(BranchTransaction $record): void
    {
        // ✅ Filament هيحقن الـ record تلقائيًا من /{record}/print
        $this->record = $record->load(['branch', 'country', 'currency', 'creator', 'approver', 'rejecter']);
    }

    // Optional: منع الدخول بدون صلاحية
    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->can('branch_tx.print') ?? false;
    }
}
