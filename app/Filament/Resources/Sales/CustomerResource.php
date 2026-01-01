<?php

namespace App\Filament\Resources\Sales;

use App\Filament\Resources\Sales\CustomerResource\Pages;
use App\Filament\Concerns\TranslatableNavigation;
use App\Models\Sales\Customer;
use App\Models\MainCore\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    use TranslatableNavigation;

    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationTranslationKey = 'menu.sales.customers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText('Unique customer code'),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Address')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('state')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('country')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Financial')
                    ->schema([
                        Forms\Components\Select::make('currency_id')
                            ->relationship('currency', 'name')
                            ->options(Currency::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('credit_limit')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('$'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('credit_limit')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Orders')
                    ->counts('orders')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
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
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->can('customers.update') ?? false),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->can('customers.delete') ?? false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->can('customers.delete') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('customers.view_any') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('customers.create') ?? false;
    }

    public static function canEdit(mixed $record): bool
    {
        return auth()->user()?->can('customers.update') ?? false;
    }

    public static function canDelete(mixed $record): bool
    {
        return auth()->user()?->can('customers.delete') ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}

