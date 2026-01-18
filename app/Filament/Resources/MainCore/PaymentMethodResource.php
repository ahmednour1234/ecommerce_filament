<?php

namespace App\Filament\Resources\MainCore;

use App\Filament\Resources\MainCore\PaymentMethodResource\Pages;
use App\Filament\Resources\MainCore\PaymentMethodResource\RelationManagers;
use App\Filament\Concerns\TranslatableNavigation;
use App\Filament\Concerns\IntegrationsModuleGate;
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
    use TranslatableNavigation, IntegrationsModuleGate;
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Integrations';
    protected static ?string $navigationTranslationKey = 'menu.integrations.payments.methods';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('provider_id')
                    ->label(tr('forms.payment_methods.provider_id.label', [], null, 'dashboard'))
                    ->relationship('provider', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('name')
                    ->label(tr('forms.payment_methods.name.label', [], null, 'dashboard'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label(tr('forms.payment_methods.code.label', [], null, 'dashboard'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('fee_fixed')
                    ->label(tr('forms.payment_methods.fee_fixed.label', [], null, 'dashboard'))
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('fee_percent')
                    ->label(tr('forms.payment_methods.fee_percent.label', [], null, 'dashboard'))
                    ->numeric()
                    ->default(0)
                    ->suffix('%'),
                Forms\Components\Toggle::make('is_default')
                    ->label(tr('forms.payment_methods.is_default.label', [], null, 'dashboard'))
                    ->default(false),
                Forms\Components\Toggle::make('is_active')
                    ->label(tr('forms.payment_methods.is_active.label', [], null, 'dashboard'))
                    ->required()
                    ->default(true),
                Forms\Components\TextInput::make('display_order')
                    ->label(tr('forms.payment_methods.display_order.label', [], null, 'dashboard'))
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider.name')
                    ->label(tr('tables.payment_methods.provider', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.payment_methods.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('tables.payment_methods.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee_fixed')
                    ->label(tr('tables.payment_methods.fee_fixed', [], null, 'dashboard'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee_percent')
                    ->label(tr('tables.payment_methods.fee_percent', [], null, 'dashboard'))
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label(tr('tables.payment_methods.is_default', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(tr('tables.payment_methods.is_active', [], null, 'dashboard'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('display_order')
                    ->label(tr('tables.payment_methods.display_order', [], null, 'dashboard'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(tr('tables.payment_methods.created_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(tr('tables.payment_methods.updated_at', [], null, 'dashboard'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label(tr('tables.payment_methods.filters.provider', [], null, 'dashboard'))
                    ->relationship('provider', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(tr('tables.payment_methods.filters.is_active', [], null, 'dashboard'))
                    ->placeholder(tr('common.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.payment_methods.filters.active_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.payment_methods.filters.inactive_only', [], null, 'dashboard')),
                Tables\Filters\TernaryFilter::make('is_default')
                    ->label(tr('tables.payment_methods.filters.is_default', [], null, 'dashboard'))
                    ->placeholder(tr('common.all', [], null, 'dashboard'))
                    ->trueLabel(tr('tables.payment_methods.filters.default_only', [], null, 'dashboard'))
                    ->falseLabel(tr('tables.payment_methods.filters.non_default_only', [], null, 'dashboard')),
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

