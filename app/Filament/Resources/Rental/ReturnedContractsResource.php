<?php

namespace App\Filament\Resources\Rental;

use App\Filament\Resources\Rental\ReturnedContractsResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Rental\RentalContract;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReturnedContractsResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RentalContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationGroup = 'rental';
    protected static ?string $navigationLabel = 'العقود المسترجعة';
    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return RentalContractResource::table($table)
            ->modifyQueryUsing(fn ($query) => $query->returned());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReturnedContracts::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return RentalContractResource::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
