<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\PaymentMethodResource\Pages;
use App\Filament\Resources\MainCore\PaymentMethodResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\MainCore\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentMethodResource extends Resource
{
    use TranslatableNavigation;
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Integrations';
    protected static ?string $navigationTranslationKey = 'menu.integrations.payments.methods';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider_id')
                    ->relationship('provider', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fee_fixed')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('fee_percent')
                    ->numeric()
                    ->default(0)
                    ->suffix('%'),
                Forms\Components\Toggle::make('is_default')
                    ->default(false),
                Forms\Components\Toggle::make('is_active')
                    ->required()
                    ->default(true),
                Forms\Components\TextInput::make('display_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee_fixed')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee_percent')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_order')
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
                Tables\Filters\SelectFilter::make('provider_id')
                    ->relationship('provider', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active')
                    ->placeholder('All')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label('Default')
                    ->placeholder('All')
                    ->trueLabel('Default only')
                    ->falseLabel('Non-default only'),
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('payment_methods.view_any') ?? true;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

