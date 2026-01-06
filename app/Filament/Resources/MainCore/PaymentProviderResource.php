<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\PaymentProviderResource\Pages;
use App\Filament\Resources\MainCore\PaymentProviderResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\PaymentProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentProviderResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = PaymentProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Integrations';
    protected static ?string $navigationTranslationKey = 'menu.integrations.payments.providers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(tr('forms.payment_providers.name.label', [], null, 'dashboard'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label(tr('forms.payment_providers.code.label', [], null, 'dashboard'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('driver')
                    ->label(tr('forms.payment_providers.driver.label', [], null, 'dashboard'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\KeyValue::make('config')
                    ->label(tr('forms.payment_providers.config.label', [], null, 'dashboard'))
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label(tr('forms.payment_providers.is_active.label', [], null, 'dashboard'))
                    ->required()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.payment_providers.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('tables.payment_providers.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('driver')
                    ->label(tr('tables.payment_providers.driver', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.payment_providers.is_active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.payment_providers.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.payment_providers.updated_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.payment_providers.filters.is_active', [], null, 'dashboard'))
                    ->placeholder(tr('common.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.payment_providers.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.payment_providers.filters.inactive_only', [], null, 'dashboard')),
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
            'index' => Pages\ListPaymentProviders::route('/'),
            'create' => Pages\CreatePaymentProvider::route('/create'),
            'edit' => Pages\EditPaymentProvider::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('payment_providers.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

