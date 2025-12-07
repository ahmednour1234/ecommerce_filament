<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\CurrencyResource\Pages;
use App\Filament\Resources\MainCore\CurrencyResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class CurrencyResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'MainCore';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(100),

            Forms\Components\TextInput::make('symbol')
                ->maxLength(10),

            Forms\Components\TextInput::make('precision')
                ->numeric()
                ->default(2)
                ->minValue(0)
                ->maxValue(6),

            Forms\Components\Toggle::make('is_default')
                ->label('Default')
                ->helperText('Only one currency should be default.'),

            Forms\Components\Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('symbol'),
                Tables\Columns\TextColumn::make('precision'),
                Tables\Columns\IconColumn::make('is_default')->boolean()->label('Default'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('currencies.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('currencies.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('currencies.delete') ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit'   => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('currencies.view_any') ?? false;
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('currencies.create') ?? false;
    }
    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('currencies.update') ?? false;
    }
    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('currencies.delete') ?? false;
    }
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('currencies.delete') ?? false;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
