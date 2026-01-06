<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\CurrencyRateResource\Pages;
use App\Filament\Resources\MainCore\CurrencyRateResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\CurrencyRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class CurrencyRateResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = CurrencyRate::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationTranslationKey = 'menu.settings.currencies';
    protected static ?int $navigationSort = 25;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('base_currency_id')
                ->label(tr('forms.currency_rates.base_currency_id.label', [], null, 'dashboard'))
                ->relationship('baseCurrency', 'code')
                ->options(Currency::pluck('code', 'id'))
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\Select::make('target_currency_id')
                ->label(tr('forms.currency_rates.target_currency_id.label', [], null, 'dashboard'))
                ->relationship('targetCurrency', 'code')
                ->options(Currency::pluck('code', 'id'))
                ->required()
                ->searchable()
                ->preload(),

            Forms\Components\TextInput::make('rate')
                ->label(tr('forms.currency_rates.rate.label', [], null, 'dashboard'))
                ->numeric()
                ->required()
                ->minValue(0.000001),

            Forms\Components\DateTimePicker::make('valid_from')
                ->label(tr('forms.currency_rates.valid_from.label', [], null, 'dashboard'))
                ->default(now())
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('baseCurrency.code')->label(tr('tables.currency_rates.base', [], null, 'dashboard')),
                Tables\Columns\TextColumn::make('targetCurrency.code')->label(tr('tables.currency_rates.target', [], null, 'dashboard')),
                Tables\Columns\TextColumn::make('rate')->label(tr('tables.currency_rates.rate', [], null, 'dashboard')),
                Tables\Columns\TextColumn::make('valid_from')->label(tr('tables.currency_rates.valid_from', [], null, 'dashboard'))->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('currency_rates.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('currency_rates.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('currency_rates.delete') ?? false),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCurrencyRates::route('/'),
            'create' => Pages\CreateCurrencyRate::route('/create'),
            'edit'   => Pages\EditCurrencyRate::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('currency_rates.view_any') ?? false;
    }
    public static function canCreate(): bool
    {
        return auth()->user()?->can('currency_rates.create') ?? false;
    }
    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('currency_rates.update') ?? false;
    }
    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('currency_rates.delete') ?? false;
    }
    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can('currency_rates.delete') ?? false;
    }
    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
