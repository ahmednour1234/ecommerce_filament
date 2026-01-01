<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\ShippingProviderResource\Pages;
use App\Filament\Resources\MainCore\ShippingProviderResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\ShippingProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShippingProviderResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = ShippingProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Integrations';
    protected static ?string $navigationTranslationKey = 'menu.integrations.shipping.providers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\KeyValue::make('config')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShippingProviders::route('/'),
            'create' => Pages\CreateShippingProvider::route('/create'),
            'edit' => Pages\EditShippingProvider::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('shipping_providers.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

