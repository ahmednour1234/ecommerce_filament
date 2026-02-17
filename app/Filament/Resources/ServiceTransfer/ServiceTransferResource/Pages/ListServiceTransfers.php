<?php

namespace App\Filament\Resources\ServiceTransfer\ServiceTransferResource\Pages;

use App\Filament\Resources\ServiceTransfer\ServiceTransferResource;
use App\Models\ServiceTransfer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListServiceTransfers extends ListRecords
{
    protected static string $resource = ServiceTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('إنشاء طلب نقل خدمة'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('الكل')
                ->badge(fn () => ServiceTransfer::count()),
            'active' => Tab::make('الطلبات النشطة')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('request_status', 'active'))
                ->badge(fn () => ServiceTransfer::where('request_status', 'active')->count()),
            'refunded' => Tab::make('الطلبات المستردة')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('request_status', 'refunded'))
                ->badge(fn () => ServiceTransfer::where('request_status', 'refunded')->count()),
            'archived' => Tab::make('الأرشيف')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('request_status', 'archived'))
                ->badge(fn () => ServiceTransfer::where('request_status', 'archived')->count()),
        ];
    }
}
