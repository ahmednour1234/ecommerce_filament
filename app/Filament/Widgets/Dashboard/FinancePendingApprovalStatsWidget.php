<?php

namespace App\Filament\Widgets\Dashboard;

use App\Filament\Resources\Finance\BranchTransactionResource;
use App\Models\Finance\BranchTransaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class FinancePendingApprovalStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 2;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        return $user->hasRole('super_admin')
            || $user->type === User::TYPE_ACCOUNTANT
            || $user->type === User::TYPE_GENERAL_ACCOUNTANT
            || $user->can('finance.view_transactions');
    }

    protected function getStats(): array
    {
        $query = BranchTransaction::query()->pending();
        $user = auth()->user();
        if ($user && ! $user->hasRole('super_admin') && ! $user->can('finance.view_all_branches')) {
            $branchIds = $user->branches()->pluck('branches.id')->toArray();
            if (! empty($branchIds)) {
                $query->whereIn('branch_id', $branchIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }
        $count = $query->count();

        return [
            Stat::make('معاملات تنتظر الموافقة', $count)
                ->description('تحتاج مراجعة وموافقة')
                ->url(BranchTransactionResource::getUrl('index', ['tableFilters' => ['status' => ['value' => 'pending']]]))
                ->color($count > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clock'),
        ];
    }
}
