<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\ShipmentResource\Pages;
use App\Filament\Resources\MainCore\ShipmentResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\Shipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShipmentResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = Shipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Integrations';
    protected static ?string $navigationTranslationKey = 'menu.integrations.shipping.shipments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('shippable_type')
                    ->label(tr('forms.shipments.shippable_type.label', [], null, 'dashboard'))
                    ->options([
                        'order' => tr('forms.shipments.shippable_type.options.order', [], null, 'dashboard'),
                        'product' => tr('forms.shipments.shippable_type.options.product', [], null, 'dashboard'),
                    ])
                    ->required(),
                Forms\Components\TextInput::make('shippable_id')
                    ->label(tr('forms.shipments.shippable_id.label', [], null, 'dashboard'))
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('shipping_provider_id')
                    ->label(tr('forms.shipments.shipping_provider_id.label', [], null, 'dashboard'))
                    ->relationship('provider', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('tracking_number')
                    ->label(tr('forms.shipments.tracking_number.label', [], null, 'dashboard'))
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label(tr('forms.shipments.status.label', [], null, 'dashboard'))
                    ->options([
                        'pending' => tr('forms.shipments.status.options.pending', [], null, 'dashboard'),
                        'processing' => tr('forms.shipments.status.options.processing', [], null, 'dashboard'),
                        'shipped' => tr('forms.shipments.status.options.shipped', [], null, 'dashboard'),
                        'delivered' => tr('forms.shipments.status.options.delivered', [], null, 'dashboard'),
                        'cancelled' => tr('forms.shipments.status.options.cancelled', [], null, 'dashboard'),
                    ])
                    ->required()
                    ->default('pending'),
                Forms\Components\Select::make('currency_id')
                    ->label(tr('forms.shipments.currency_id.label', [], null, 'dashboard'))
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('price')
                    ->label(tr('forms.shipments.price.label', [], null, 'dashboard'))
                    ->numeric()
                    ->default(0),
                Forms\Components\KeyValue::make('meta')
                    ->label(tr('forms.shipments.meta.label', [], null, 'dashboard'))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('shippable_type')
                    ->label(tr('tables.shipments.shippable_type', [], null, 'dashboard'))
                    ->formatStateUsing(fn (string $state): string => tr('forms.shipments.shippable_type.options.' . $state, [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shippable_id')
                    ->label(tr('tables.shipments.shippable_id', [], null, 'dashboard'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('provider.name')
                    ->label(tr('tables.shipments.provider', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label(tr('tables.shipments.tracking_number', [], null, 'dashboard'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(tr('tables.shipments.status', [], null, 'dashboard'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => tr('forms.shipments.status.options.' . $state, [], null, 'dashboard'))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->label(tr('tables.shipments.currency', [], null, 'dashboard'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label(tr('tables.shipments.price', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.shipments.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.shipments.updated_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(tr('tables.shipments.filters.status', [], null, 'dashboard'))
                    ->options([
                        'pending' => tr('forms.shipments.status.options.pending', [], null, 'dashboard'),
                        'processing' => tr('forms.shipments.status.options.processing', [], null, 'dashboard'),
                        'shipped' => tr('forms.shipments.status.options.shipped', [], null, 'dashboard'),
                        'delivered' => tr('forms.shipments.status.options.delivered', [], null, 'dashboard'),
                        'cancelled' => tr('forms.shipments.status.options.cancelled', [], null, 'dashboard'),
                    ]),
                Tables\Filters\SelectFilter::make('shipping_provider_id')
                    ->label(tr('tables.shipments.filters.provider', [], null, 'dashboard'))
                    ->relationship('provider', 'name'),
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
            'index' => Pages\ListShipments::route('/'),
            'create' => Pages\CreateShipment::route('/create'),
            'edit' => Pages\EditShipment::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('shipments.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

