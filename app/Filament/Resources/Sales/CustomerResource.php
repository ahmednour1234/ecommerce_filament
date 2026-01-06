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
                Forms\Components\Section::make(trans_dash('forms.customers.sections.basic_information'))
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label(trans_dash('forms.customers.code.label'))
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->helperText(trans_dash('forms.customers.code.helper_text')),

                        Forms\Components\TextInput::make('name')
                            ->label(trans_dash('forms.customers.name.label'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(trans_dash('forms.customers.email.label'))
                            ->email()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label(trans_dash('forms.customers.phone.label'))
                            ->tel()
                            ->maxLength(50),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(trans_dash('forms.customers.sections.address'))
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label(trans_dash('forms.customers.address.label'))
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('city')
                            ->label(trans_dash('forms.customers.city.label'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('state')
                            ->label(trans_dash('forms.customers.state.label'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('country')
                            ->label(trans_dash('forms.customers.country.label'))
                            ->maxLength(100),

                        Forms\Components\TextInput::make('postal_code')
                            ->label(trans_dash('forms.customers.postal_code.label'))
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(trans_dash('forms.customers.sections.financial'))
                    ->schema([
                        Forms\Components\Select::make('currency_id')
                            ->label(trans_dash('forms.customers.currency_id.label'))
                            ->relationship('currency', 'name')
                            ->options(Currency::active()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('credit_limit')
                            ->label(trans_dash('forms.customers.credit_limit.label'))
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('$'),

                        Forms\Components\Toggle::make('is_active')
                            ->label(trans_dash('forms.customers.is_active.label'))
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
                    ->label(trans_dash('tables.customers.code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(trans_dash('tables.customers.name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label(trans_dash('tables.customers.email'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label(trans_dash('tables.customers.phone'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('credit_limit')
                    ->label(trans_dash('tables.customers.credit_limit'))
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('orders_count')
                    ->label(trans_dash('tables.customers.orders'))
                    ->counts('orders')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(trans_dash('tables.customers.is_active'))
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(trans_dash('filters.customers.is_active.label'))
                    ->placeholder(trans_dash('filters.customers.is_active.placeholder'))
                    ->trueLabel(trans_dash('filters.customers.is_active.true_label'))
                    ->falseLabel(trans_dash('filters.customers.is_active.false_label')),
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

