<?php

namespace App\Filament\Widgets\Rental;

use App\Filament\Resources\Rental\RentalContractResource;
use App\Models\Rental\RentalContract;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContractStatsWidget extends BaseWidget
{
    protected static ?string $navigationGroup = 'قسم عقود الايجار';
    protected static ?int $sort = 41;
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user?->hasRole('super_admin') || $user?->can('rental.contracts.view_any') ?? false;
    }

    protected function getStats(): array
    {
        $active           = RentalContract::where('status', 'active')->count();
        $pendingApproval  = RentalContract::where('status', 'pending_approval')->count();

        return [
            Stat::make(tr('rental.stats.active_contracts', [], null, 'dashboard') ?: 'العقود النشطة', $active)
                ->description(tr('rental.stats.active_contracts_desc', [], null, 'dashboard') ?: 'عقود تأجير نشطة')
                ->color('success')
                ->icon('heroicon-o-document-check')
                ->url(RentalContractResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'active'],
                    ],
                ])),

            Stat::make(tr('rental.stats.pending_approval', [], null, 'dashboard') ?: 'ينتظر الموافقة', $pendingApproval)
                ->description(tr('rental.stats.pending_approval_desc', [], null, 'dashboard') ?: 'عقود تنتظر موافقة صاحب الشركة')
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->url(RentalContractResource::getUrl('index', [
                    'tableFilters' => [
                        'status' => ['value' => 'pending_approval'],
                    ],
                ])),
        ];
    }
}
