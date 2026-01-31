<?php

namespace App\Filament\Resources\Rental;

use App\Filament\Resources\Rental\ArchivedContractsResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Rental\RentalContract;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArchivedContractsResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = RentalContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'قسم التأجير';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationTranslationKey = 'navigation.rental_archived';

    public static function table(Table $table): Table
    {
        return RentalContractResource::table($table)
            ->modifyQueryUsing(fn ($query) => $query->archived());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArchivedContracts::route('/'),
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
